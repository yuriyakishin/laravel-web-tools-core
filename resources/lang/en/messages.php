<?php

return [
    'report' => [
        'again' => 'New check',
        'disclaimer' => 'The score follows the tool\'s own rules; it is not an official certification, a browser guarantee or a search ranking.',
        'found' => 'What was found',
        'fix' => 'How to fix',
        'copy' => 'Copy',
        'copied' => 'Copied',
        'ruleset' => 'ruleset :ruleset',
    ],
    'status' => [
        'pass' => 'Passed',
        'warn' => 'Partly',
        'fail' => 'Failed',
        'skip' => 'Skipped',
    ],
    'errors' => [
        'invalid_url' => 'Enter the full page address, for example https://example.com/page',
        'blocked_host' => 'This address cannot be checked: the page must be reachable from the internet',
        'unreachable' => 'Could not open the site. Check the address',
        'timeout' => 'The site did not respond in time. Please try again',
        'http_error' => 'The site responded with error :status. The page may be closed to bots',
        'too_large' => 'The page is too large to check',
        'too_many_redirects' => 'Too many redirects',
        'rate_limited' => 'Too many checks. Try again in :minutes min',
        'disabled' => 'The service is temporarily unavailable',
        'generic' => 'Something went wrong. Please try again',
        'unsupported_type' => 'This address is not an HTML page',
    ],
    'scale' => [
        'not_ready' => 'Not ready',
        'partly' => 'Partly',
        'almost' => 'Almost',
        'ready' => 'Ready',
    ],
];
