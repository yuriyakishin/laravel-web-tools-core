<?php

namespace Yu\WebToolsCore\Http;

use Illuminate\Support\Str;
use Yu\WebToolsCore\Exceptions\ToolException;

/**
 * Lets a tool reach only public addresses (SSRF): http or https on ports 80 and 443, no credentials,
 * and a host whose every IP is public. Callers connect to the returned IP, so DNS cannot answer differently later.
 */
class UrlGuard
{
    private const array BLOCKED_SUFFIXES = ['.local', '.internal', '.localhost'];

    public function __construct(private HostResolver $resolver) {}

    /**
     * Validates the URL and returns the host, port and the checked public IP to connect to.
     *
     * @return array{0: string, 1: int, 2: string}
     *
     * @throws ToolException invalid_url, blocked_host, unreachable
     */
    public function check(string $url): array
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower(trim($parts['host'] ?? '', '[]'));
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        if (!in_array($scheme, ['http', 'https'], true)
            || $host === ''
            || isset($parts['user'])
            || isset($parts['pass'])
            || !in_array($port, [80, 443], true)) {
            throw new ToolException('invalid_url');
        }

        $isIp = filter_var($host, FILTER_VALIDATE_IP) !== false;

        if (!$isIp && (!str_contains($host, '.') || Str::endsWith($host, self::BLOCKED_SUFFIXES))) {
            throw new ToolException('blocked_host');
        }

        $ips = $isIp ? [$host] : $this->resolver->resolve($host);

        if ($ips === []) {
            throw new ToolException('unreachable');
        }

        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_GLOBAL_RANGE) === false) {
                throw new ToolException('blocked_host');
            }
        }

        return [$host, $port, $ips[0]];
    }
}
