<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Http;

/**
 * The answer to one request of Prober: the status and headers, or why there was no answer.
 */
final readonly class ProbeResult
{
    /**
     * @param  int  $status  0 when the server did not answer
     * @param  array<string, string>  $headers  names in lower case
     * @param  string|null  $location  absolute address of a redirect
     * @param  string|null  $error  a ToolException reason when there was no answer: blocked_host, unreachable, timeout, too_large
     * @param  int  $ms  time of the batch the request was sent in
     */
    public function __construct(
        public string $url,
        public int $status = 0,
        public array $headers = [],
        public ?string $location = null,
        public string $body = '',
        public ?string $error = null,
        public int $ms = 0,
    ) {}

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function answered(): bool
    {
        return $this->status > 0;
    }

    public function isOk(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function isRedirect(): bool
    {
        return $this->status >= 300 && $this->status < 400 && $this->location !== null;
    }

    /**
     * Sites that answer bots with "forbidden" or "too many requests" (LinkedIn even uses 999): the address
     * may work for people, so it is reported as not checked rather than broken.
     */
    public function isBlocked(): bool
    {
        return in_array($this->status, [401, 403, 429, 999], true);
    }
}
