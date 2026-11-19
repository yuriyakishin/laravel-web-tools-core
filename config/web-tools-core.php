<?php

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
];
