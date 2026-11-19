<?php

namespace Yu\WebToolsCore\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * A visitor finished a check in one of the web tools, successful or not, fresh or from the cache.
 */
class ToolCheckedEvent
{
    use Dispatchable;

    /**
     * @param  string  $tool  the tool name, e.g. "webmcp-analyzer"
     * @param  array<string, mixed>|null  $report  the report array; null when the check failed
     * @param  string|null  $error  reason of the failure, e.g. "timeout"; null on success
     */
    public function __construct(
        public readonly string $tool,
        public readonly string $url,
        public readonly string $locale,
        public readonly ?array $report = null,
        public readonly ?string $error = null,
        public readonly bool $fromCache = false,
    ) {}
}
