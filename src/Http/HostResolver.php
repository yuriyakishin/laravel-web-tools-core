<?php

namespace Yu\WebToolsCore\Http;

class HostResolver
{
    /**
     * All IPv4 and IPv6 addresses of the host; empty when DNS has none.
     *
     * @return list<string>
     */
    public function resolve(string $host): array
    {
        $records = @dns_get_record($host, DNS_A | DNS_AAAA) ?: [];
        $ips = [];

        foreach ($records as $record) {
            $ips[] = $record['ip'] ?? $record['ipv6'];
        }

        return $ips;
    }
}
