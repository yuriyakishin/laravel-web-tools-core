<?php

namespace Yu\WebToolsCore\Http;

/**
 * The TLS certificate a site showed and the connection it was shown on.
 */
final readonly class Certificate
{
    /**
     * @param  bool  $trusted  the chain leads to a known authority and the name matches the host
     * @param  list<string>  $names  the DNS names the certificate covers, wildcards included
     * @param  string  $protocol  the agreed TLS version, e.g. "TLSv1.3"
     */
    public function __construct(
        public bool $trusted,
        public string $issuer,
        public array $names,
        public int $validFrom,
        public int $validTo,
        public string $protocol,
    ) {}

    /**
     * Whether a name of the certificate matches the host; a wildcard covers exactly one label.
     */
    public function covers(string $host): bool
    {
        $host = strtolower($host);

        foreach ($this->names as $name) {
            $name = strtolower($name);

            if ($name === $host) {
                return true;
            }

            if (str_starts_with($name, '*.') && substr_count($host, '.') === substr_count($name, '.') && str_ends_with($host, substr($name, 1))) {
                return true;
            }
        }

        return false;
    }

    public function isExpired(): bool
    {
        return $this->validTo < now()->getTimestamp();
    }

    /**
     * Whole days left until the certificate expires; negative after it has.
     */
    public function daysLeft(): int
    {
        return (int) floor(($this->validTo - now()->getTimestamp()) / 86400);
    }

    /**
     * @return array{trusted: bool, issuer: string, names: list<string>, validFrom: string, validTo: string, protocol: string}
     */
    public function toArray(): array
    {
        return [
            ...get_object_vars($this),
            'validFrom' => date(DATE_ATOM, $this->validFrom),
            'validTo' => date(DATE_ATOM, $this->validTo),
        ];
    }
}
