<?php

namespace Yu\WebToolsCore\Checklist;

/**
 * Result of one check; a skipped check does not count, neither in the points nor in the maximum.
 */
enum CheckStatus: string
{
    case Pass = 'pass';
    case Warn = 'warn';
    case Fail = 'fail';
    case Skip = 'skip';
}
