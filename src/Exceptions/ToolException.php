<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Exceptions;

use RuntimeException;
use Throwable;

/**
 * An expected failure of a tool check; $reason is the key of its translated message, e.g. "timeout".
 */
class ToolException extends RuntimeException
{
    /**
     * @param  array<string, string|int>  $replace  Placeholders for the translated message.
     */
    public function __construct(
        public readonly string $reason,
        public readonly array $replace = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($reason, 0, $previous);
    }
}
