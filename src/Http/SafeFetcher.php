<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Http;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Yu\WebToolsCore\Exceptions\ToolException;

/**
 * Downloads a public URL for a tool without letting it reach the server's own network (SSRF):
 * every address, redirects included, must resolve to public IPs only, and the connection goes to the checked IP.
 */
class SafeFetcher
{
    public function __construct(private UrlGuard $guard) {}

    /**
     * Follows redirects and returns the final response of any status; the caller decides which statuses it accepts.
     *
     * @param  list<string>|null  $types  allowed content types, e.g. ['text/html']; null allows any
     *
     * @throws ToolException invalid_url, blocked_host, unreachable, timeout, too_many_redirects, too_large, unsupported_type
     */
    public function fetch(string $url, ?array $types, int $maxBytes, string $accept = '*/*'): Resource
    {
        $trace = $this->trace($url, $types, $maxBytes, $accept);

        if ($trace['stopped'] !== null) {
            throw new ToolException('too_many_redirects');
        }

        return $trace['resource'];
    }

    /**
     * Follows redirects one by one and records every hop. A loop or a chain longer than $maxRedirects
     * does not throw: the trace ends with "stopped" set to "loop" or "too_many_redirects" and no resource.
     *
     * @param  list<string>|null  $types
     * @param  int|null  $maxRedirects  defaults to config('web-tools-core.fetch.max_redirects')
     * @return array{hops: list<array{url: string, status: int, location: ?string, ms: int}>, resource: ?resource, stopped: ?string}
     *
     * @throws ToolException invalid_url, blocked_host, unreachable, timeout, http_error, too_large, unsupported_type
     */
    public function trace(string $url, ?array $types, int $maxBytes, string $accept = '*/*', ?int $maxRedirects = null): array
    {
        $maxRedirects ??= (int) config('web-tools-core.fetch.max_redirects');
        $hops = [];

        for ($hop = 0; $hop <= $maxRedirects; $hop++) {
            $started = hrtime(true);
            $response = $this->request($url, $maxBytes, $accept);
            $ms = (int) round((hrtime(true) - $started) / 1e6);

            if (!$response->redirect()) {
                $hops[] = ['url' => $url, 'status' => $response->status(), 'location' => null, 'ms' => $ms];

                return ['hops' => $hops, 'resource' => $this->resource($url, $response, $types, $maxBytes), 'stopped' => null];
            }

            $location = $response->header('Location');

            if ($location === '') {
                throw new ToolException('http_error', ['status' => $response->status()]);
            }

            $next = (string) UriResolver::resolve(new Uri($url), new Uri($location));
            $hops[] = ['url' => $url, 'status' => $response->status(), 'location' => $next, 'ms' => $ms];

            if (in_array($next, array_column($hops, 'url'), true)) {
                return ['hops' => $hops, 'resource' => null, 'stopped' => 'loop'];
            }

            $url = $next;
        }

        return ['hops' => $hops, 'resource' => null, 'stopped' => 'too_many_redirects'];
    }

    private function request(string $url, int $maxBytes, string $accept): Response
    {
        [$host, $port, $ip] = $this->guard->check($url);
        $config = config('web-tools-core.fetch');
        $address = str_contains($ip, ':') ? "[{$ip}]" : $ip;
        $tooLarge = false;

        try {
            return Http::withoutRedirecting()
                ->withUserAgent($config['user_agent'])
                ->withHeaders(['Accept' => $accept, 'Accept-Language' => app()->getLocale()])
                ->connectTimeout($config['connect_timeout'])
                ->timeout($config['timeout'])
                ->withOptions([
                    'protocols' => ['http', 'https'],
                    // Stops the download as soon as the file is bigger than allowed, even without Content-Length
                    'progress' => function (int $downloadTotal, int $downloaded) use ($maxBytes, &$tooLarge): bool {
                        return $tooLarge = $downloaded > $maxBytes;
                    },
                    // Connect to the IP checked above, so DNS cannot answer differently the second time.
                    // No "stream" option: with it Guzzle 8 switches to its stream handler, which rejects cURL options.
                    'curl' => [CURLOPT_RESOLVE => ["{$host}:{$port}:{$address}"]],
                ])
                ->get($url);
        } catch (RequestException $e) {
            // A transfer error after the status line arrived (e.g. a 404 that closes the connection early):
            // the response is all a tool needs, and the caller decides which statuses it accepts
            if ($tooLarge) {
                throw new ToolException('too_large', previous: $e);
            }

            return $e->response;
        } catch (ConnectionException|TransferException $e) {
            if ($tooLarge) {
                throw new ToolException('too_large', previous: $e);
            }

            $reason = Str::contains($e->getMessage(), 'timed out', true) ? 'timeout' : 'unreachable';

            throw new ToolException($reason, previous: $e);
        }
    }

    /**
     * @param  list<string>|null  $types
     */
    private function resource(string $url, Response $response, ?array $types, int $maxBytes): Resource
    {
        $contentType = strtolower($response->header('Content-Type'));

        if ($types !== null && $response->successful() && !Str::contains($contentType, $types)) {
            throw new ToolException('unsupported_type');
        }

        // The download is already cut at $maxBytes by the "progress" option; this catches what slipped past it
        $body = $response->body();
        if ((int) $response->header('Content-Length') > $maxBytes || strlen($body) > $maxBytes) {
            throw new ToolException('too_large');
        }

        preg_match('/charset=([\w-]+)/', $contentType, $charset);

        $headers = [];
        foreach ($response->headers() as $name => $values) {
            // Cookies go one per line: their Expires dates have commas of their own
            $name = strtolower($name);
            $headers[$name] = implode($name === 'set-cookie' ? "\n" : ', ', $values);
        }

        return new Resource($url, $response->status(), $headers, $body, $contentType, $charset[1] ?? null);
    }
}
