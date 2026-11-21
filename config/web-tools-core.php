<?php

declare(strict_types=1);

return [
    'fetch' => [
        'connect_timeout' => 4,
        'timeout' => 8,
        'max_redirects' => 3,
        'user_agent' => env(
            'WEB_TOOLS_USER_AGENT',
            'Mozilla/5.0 (compatible; YuWebTools/1.0; +https://yu.net.ua/tools)'
        ),
    ],

    /* Prober: many URLs at once (links, images, pages of a site). */
    'probe' => [
        'concurrency' => 8,
        'connect_timeout' => 3,
        'timeout' => 6,
    ],
];
