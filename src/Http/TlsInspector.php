<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Http;

use Yu\WebToolsCore\Exceptions\ToolException;

/**
 * Opens a TLS connection to port 443 of a public host and reads its certificate.
 * The connection goes to the IP checked by UrlGuard; the host name is sent for SNI and the name check.
 */
class TlsInspector
{
    public function __construct(private UrlGuard $guard) {}

    /**
     * The certificate, or null when the host does not answer TLS on port 443.
     * An untrusted certificate is read too, with $trusted = false, so that the report can say what is wrong with it.
     *
     * @throws ToolException invalid_url, blocked_host, unreachable
     */
    public function inspect(string $host): ?Certificate
    {
        [$host, , $ip] = $this->guard->check('https://'.$host.'/');

        return $this->connect($host, $ip, verify: true) ?? $this->connect($host, $ip, verify: false);
    }

    private function connect(string $host, string $ip, bool $verify): ?Certificate
    {
        $address = str_contains($ip, ':') ? "[{$ip}]" : $ip;
        $context = stream_context_create(['ssl' => [
            'peer_name' => $host,
            'SNI_enabled' => true,
            'verify_peer' => $verify,
            'verify_peer_name' => $verify,
            'allow_self_signed' => !$verify,
            'capture_peer_cert' => true,
        ]]);

        $client = @stream_socket_client(
            "ssl://{$address}:443",
            $errorCode,
            $errorMessage,
            (float) config('web-tools-core.fetch.connect_timeout'),
            STREAM_CLIENT_CONNECT,
            $context,
        );

        if ($client === false) {
            return null;
        }

        $certificate = stream_context_get_params($client)['options']['ssl']['peer_certificate'] ?? null;
        $protocol = stream_get_meta_data($client)['crypto']['protocol'] ?? '';
        fclose($client);

        $fields = $certificate === null ? false : openssl_x509_parse($certificate);

        if ($fields === false) {
            return null;
        }

        return new Certificate(
            trusted: $verify,
            issuer: $fields['issuer']['O'] ?? $fields['issuer']['CN'] ?? '',
            names: $this->names($fields),
            validFrom: (int) $fields['validFrom_time_t'],
            validTo: (int) $fields['validTo_time_t'],
            protocol: $protocol,
        );
    }

    /**
     * The DNS names from subjectAltName, or the subject CN when there are none.
     *
     * @param  array<string, mixed>  $fields
     * @return list<string>
     */
    private function names(array $fields): array
    {
        preg_match_all('/DNS:([^,\s]+)/', $fields['extensions']['subjectAltName'] ?? '', $matches);

        return $matches[1] ?: array_values(array_filter([$fields['subject']['CN'] ?? null]));
    }
}
