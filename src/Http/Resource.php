<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Http;

/**
 * A downloaded file: a page, a script or a text file like robots.txt.
 */
final readonly class Resource
{
    /**
     * @param  array<string, string>  $headers  response headers, names in lower case; several Set-Cookie headers are joined by new lines
     */
    public function __construct(
        public string $url,
        public int $status,
        public array $headers,
        public string $body,
        public string $contentType,
        public ?string $charset = null,
    ) {}

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }
}
