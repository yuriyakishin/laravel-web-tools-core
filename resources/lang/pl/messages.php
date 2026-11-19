<?php

declare(strict_types=1);

return [
    'report' => [
        'again' => 'Nowe sprawdzenie',
        'disclaimer' => 'Wynik opiera się na własnych regułach narzędzia; to nie oficjalny certyfikat, gwarancja przeglądarki ani pozycja w wyszukiwarce.',
        'found' => 'Co znaleziono',
        'fix' => 'Jak poprawić',
        'copy' => 'Kopiuj',
        'copied' => 'Skopiowano',
        'ruleset' => 'reguły :ruleset',
    ],
    'status' => [
        'pass' => 'Zaliczone',
        'warn' => 'Częściowo',
        'fail' => 'Niezaliczone',
        'skip' => 'Pominięte',
    ],
    'errors' => [
        'invalid_url' => 'Podaj pełny adres strony, np. https://example.com/page',
        'blocked_host' => 'Tego adresu nie można sprawdzić: strona musi być dostępna z internetu',
        'unreachable' => 'Nie udało się otworzyć strony. Sprawdź adres',
        'timeout' => 'Strona nie odpowiedziała na czas. Spróbuj ponownie',
        'http_error' => 'Strona odpowiedziała błędem :status. Możliwe, że jest zamknięta dla botów',
        'too_large' => 'Strona jest zbyt duża do sprawdzenia',
        'too_many_redirects' => 'Zbyt wiele przekierowań',
        'rate_limited' => 'Zbyt wiele sprawdzeń. Spróbuj za :minutes min',
        'disabled' => 'Usługa jest chwilowo niedostępna',
        'generic' => 'Coś poszło nie tak. Spróbuj ponownie',
        'unsupported_type' => 'Pod tym adresem nie ma strony HTML',
    ],
    'scale' => [
        'not_ready' => 'Nie gotowy',
        'partly' => 'Częściowo',
        'almost' => 'Prawie',
        'ready' => 'Gotowy',
    ],
];
