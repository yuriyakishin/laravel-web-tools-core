<?php

return [
    'report' => [
        'again' => '重新检查',
        'disclaimer' => '评分基于本工具自己的规则，不是官方认证、浏览器保证或搜索排名。',
        'found' => '发现的内容',
        'fix' => '如何修复',
        'copy' => '复制',
        'copied' => '已复制',
        'ruleset' => '规则 :ruleset',
    ],
    'status' => [
        'pass' => '通过',
        'warn' => '部分通过',
        'fail' => '未通过',
        'skip' => '已跳过',
    ],
    'errors' => [
        'invalid_url' => '请输入完整的页面地址，例如 https://example.com/page',
        'blocked_host' => '无法检查此地址：页面必须能从互联网访问',
        'unreachable' => '无法打开网站。请检查地址',
        'timeout' => '网站未及时响应。请重试',
        'http_error' => '网站返回错误 :status。页面可能禁止机器人访问',
        'too_large' => '页面过大，无法检查',
        'too_many_redirects' => '重定向次数过多',
        'rate_limited' => '检查次数过多。请 :minutes 分钟后再试',
        'disabled' => '服务暂时不可用',
        'generic' => '出了点问题。请重试',
        'unsupported_type' => '此地址不是 HTML 页面',
    ],
    'scale' => [
        'not_ready' => '未就绪',
        'partly' => '部分',
        'almost' => '接近',
        'ready' => '就绪',
    ],
];
