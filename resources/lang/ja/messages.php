<?php

declare(strict_types=1);

return [
    'report' => [
        'again' => '新しいチェック',
        'disclaimer' => 'スコアはこのツール独自のルールによるもので、公式認定、ブラウザの保証、検索順位ではありません。',
        'found' => '検出内容',
        'fix' => '修正方法',
        'copy' => 'コピー',
        'copied' => 'コピーしました',
        'ruleset' => 'ルールセット :ruleset',
    ],
    'status' => [
        'pass' => '合格',
        'warn' => '一部',
        'fail' => '不合格',
        'skip' => 'スキップ',
    ],
    'errors' => [
        'invalid_url' => 'ページの完全なURLを入力してください（例：https://example.com/page）',
        'blocked_host' => 'このURLはチェックできません：インターネットからアクセスできるページである必要があります',
        'unreachable' => 'サイトを開けませんでした。URLを確認してください',
        'timeout' => 'サイトが時間内に応答しませんでした。もう一度お試しください',
        'http_error' => 'サイトがエラー:statusを返しました。ページがボットに対して閉じられている可能性があります',
        'too_large' => 'ページが大きすぎてチェックできません',
        'too_many_redirects' => 'リダイレクトが多すぎます',
        'rate_limited' => 'チェック回数が多すぎます。:minutes分後にお試しください',
        'disabled' => 'サービスは一時的に利用できません',
        'generic' => '問題が発生しました。もう一度お試しください',
        'unsupported_type' => 'このURLはHTMLページではありません',
    ],
    'scale' => [
        'not_ready' => '未対応',
        'partly' => '一部',
        'almost' => 'ほぼ',
        'ready' => '対応済み',
    ],
];
