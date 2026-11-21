<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Http;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;
use Yu\WebToolsCore\Exceptions\ToolException;

/**
 * Requests many public URLs at once, for tools that check every link, image or page of a site. Each URL passes
 * the same SSRF rule as SafeFetcher and the connection goes to the checked IP. Redirects are not followed:
 * a result keeps the status and the Location, and the tool decides what a redirect means.
 */
class Prober
{
    public function __construct(private UrlGuard $guard) {}

    /**
     * One result per URL, in the order given.
     *
     * HEAD requests that the server refuses (405, 501, and 403/404 that some servers give HEAD only) are repeated as GET.
     * A GET downloads at most $maxBytes; with 0 the body is not kept.
     *
     * @param  list<string>  $urls
     * @param  array<string, string>  $headers
     * @return array<string, ProbeResult> keyed by URL
     */
    public function probe(array $urls, string $method = 'HEAD', array $headers = [], int $maxBytes = 0): array
    {
        $urls = array_values(array_unique($urls));
        $results = [];
        $targets = [];

        foreach ($urls as $url) {
            try {
                $targets[$url] = $this->guard->check($url);
            } catch (ToolException $e) {
                $results[$url] = new ProbeResult($url, error: $e->reason);
            }
        }

        $responses = $this->send($targets, $method, $headers, $maxBytes);

        if ($method === 'HEAD') {
            $retry = array_filter($responses, fn (ProbeResult $result): bool => in_array($result->status, [403, 404, 405, 501], true));
            $responses = [...$responses, ...$this->send(array_intersect_key($targets, $retry), 'GET', $headers, $maxBytes)];
        }

        // Some servers drop requests that come many at once: ask once more, a few at a time, before calling the address dead
        $failed = array_filter($responses, fn (ProbeResult $result): bool => !$result->answered());

        if ($failed !== []) {
            $responses = [...$responses, ...$this->send(array_intersect_key($targets, $failed), 'GET', $headers, $maxBytes, 2)];
        }

        return array_combine($urls, array_map(fn (string $url): ProbeResult => $results[$url] ?? $responses[$url], $urls));
    }

    /**
     * @param  array<string, array{0: string, 1: int, 2: string}>  $targets  URL => [host, port, ip] from the guard
     * @param  array<string, string>  $headers
     * @return array<string, ProbeResult>
     */
    private function send(array $targets, string $method, array $headers, int $maxBytes, ?int $concurrency = null): array
    {
        $config = config('web-tools-core.probe');
        $results = [];

        foreach (array_chunk($targets, max(1, $concurrency ?? (int) $config['concurrency']), true) as $chunk) {
            $started = hrtime(true);
            $tooLarge = [];
            $captured = [];

            $responses = Http::pool(function (Pool $pool) use ($chunk, $method, $headers, $maxBytes, $config, &$tooLarge, &$captured): array {
                $requests = [];

                foreach ($chunk as $url => [$host, $port, $ip]) {
                    $address = str_contains($ip, ':') ? "[{$ip}]" : $ip;
                    $request = $pool->as($url)
                        ->withoutRedirecting()
                        ->withUserAgent(config('web-tools-core.fetch.user_agent'))
                        ->withHeaders(['Accept' => '*/*', 'Accept-Language' => app()->getLocale(), ...$headers])
                        ->connectTimeout($config['connect_timeout'])
                        ->timeout($config['timeout'])
                        ->withOptions([
                            'protocols' => ['http', 'https'],
                            // The status and headers are kept as soon as they arrive; a GET for the status only stops there
                            'on_headers' => function (ResponseInterface $response) use ($url, $method, $maxBytes, &$captured): void {
                                $captured[$url] = $response;

                                if ($method === 'GET' && $maxBytes === 0) {
                                    throw new RuntimeException('Headers only');
                                }
                            },
                            // A GET for the body stops past $maxBytes
                            'progress' => function (int $total, int $downloaded) use ($url, $maxBytes, &$tooLarge): bool {
                                return $tooLarge[$url] = $maxBytes > 0 && $downloaded > $maxBytes;
                            },
                            'curl' => [CURLOPT_RESOLVE => ["{$host}:{$port}:{$address}"]],
                        ]);

                    $requests[] = $method === 'HEAD' ? $request->head($url) : $request->get($url);
                }

                return $requests;
            });

            $ms = (int) round((hrtime(true) - $started) / 1e6);

            foreach (array_keys($chunk) as $url) {
                $response = $responses[$url] ?? null;

                // A transfer stopped on purpose (headers only, or past the size limit) still answered with these headers
                if (!$response instanceof Response && isset($captured[$url]) && !($tooLarge[$url] ?? false)) {
                    $response = new Response($captured[$url]);
                }

                $results[$url] = $this->result($url, $response, $ms, $maxBytes, $tooLarge[$url] ?? false);
            }
        }

        return $results;
    }

    private function result(string $url, mixed $response, int $ms, int $maxBytes, bool $cut): ProbeResult
    {
        if ($response instanceof Throwable && method_exists($response, 'getResponse') && $response->getResponse() !== null) {
            $response = new Response($response->getResponse());
        }

        if (!$response instanceof Response) {
            $message = $response instanceof Throwable ? $response->getMessage() : '';

            // A status outside 100–599 (LinkedIn's 999 for bots) cannot become a response object; it is still an answer
            if (Str::contains($message, 'creating the response')) {
                return new ProbeResult($url, 999, ms: $ms);
            }

            return new ProbeResult($url, error: $cut ? 'too_large' : (Str::contains($message, 'timed out', true) ? 'timeout' : 'unreachable'), ms: $ms);
        }

        $headers = [];
        foreach ($response->headers() as $name => $values) {
            $name = strtolower($name);
            $headers[$name] = implode($name === 'set-cookie' ? "\n" : ', ', $values);
        }

        // Guzzle decodes gzip and Brotli and moves the original headers aside; tools need what the server sent
        foreach (['content-encoding', 'content-length'] as $name) {
            if (isset($headers['x-encoded-'.$name])) {
                $headers[$name] = $headers['x-encoded-'.$name];
            }
        }

        $location = $response->header('Location');

        return new ProbeResult(
            url: $url,
            status: $response->status(),
            headers: $headers,
            location: $location === '' ? null : (string) UriResolver::resolve(new Uri($url), new Uri($location)),
            body: $maxBytes > 0 ? substr($response->body(), 0, $maxBytes) : '',
            ms: $ms,
        );
    }
}
