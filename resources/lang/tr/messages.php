<?php

declare(strict_types=1);

return [
    'report' => [
        'again' => 'Yeni kontrol',
        'disclaimer' => 'Puan aracın kendi kurallarına dayanır; resmi bir sertifika, tarayıcı garantisi veya arama sıralaması değildir.',
        'found' => 'Bulunanlar',
        'fix' => 'Nasıl düzeltilir',
        'copy' => 'Kopyala',
        'copied' => 'Kopyalandı',
        'ruleset' => 'kurallar :ruleset',
    ],
    'status' => [
        'pass' => 'Geçti',
        'warn' => 'Kısmen',
        'fail' => 'Geçmedi',
        'skip' => 'Atlandı',
    ],
    'errors' => [
        'invalid_url' => 'Sayfanın tam adresini girin, örneğin https://example.com/page',
        'blocked_host' => 'Bu adres kontrol edilemez: sayfa internetten erişilebilir olmalı',
        'unreachable' => 'Site açılamadı. Adresi kontrol edin',
        'timeout' => 'Site zamanında yanıt vermedi. Tekrar deneyin',
        'http_error' => 'Site :status hatasıyla yanıt verdi. Sayfa botlara kapalı olabilir',
        'too_large' => 'Sayfa kontrol için çok büyük',
        'too_many_redirects' => 'Çok fazla yönlendirme',
        'rate_limited' => 'Çok fazla kontrol. :minutes dk sonra tekrar deneyin',
        'disabled' => 'Hizmet geçici olarak kullanılamıyor',
        'generic' => 'Bir şeyler ters gitti. Tekrar deneyin',
        'unsupported_type' => 'Bu adres bir HTML sayfası değil',
    ],
    'scale' => [
        'not_ready' => 'Hazır değil',
        'partly' => 'Kısmen',
        'almost' => 'Neredeyse',
        'ready' => 'Hazır',
    ],
];
