<?php

return [
    'report' => [
        'again' => 'Nová kontrola',
        'disclaimer' => 'Skóre se řídí vlastními pravidly nástroje; není to oficiální certifikace, záruka prohlížeče ani pozice ve vyhledávání.',
        'found' => 'Co bylo nalezeno',
        'fix' => 'Jak opravit',
        'copy' => 'Kopírovat',
        'copied' => 'Zkopírováno',
        'ruleset' => 'pravidla :ruleset',
    ],
    'status' => [
        'pass' => 'Splněno',
        'warn' => 'Částečně',
        'fail' => 'Nesplněno',
        'skip' => 'Přeskočeno',
    ],
    'errors' => [
        'invalid_url' => 'Zadejte úplnou adresu stránky, například https://example.com/page',
        'blocked_host' => 'Tuto adresu nelze zkontrolovat: stránka musí být dostupná z internetu',
        'unreachable' => 'Web se nepodařilo otevřít. Zkontrolujte adresu',
        'timeout' => 'Web neodpověděl včas. Zkuste to znovu',
        'http_error' => 'Web odpověděl chybou :status. Stránka může být pro roboty uzavřená',
        'too_large' => 'Stránka je pro kontrolu příliš velká',
        'too_many_redirects' => 'Příliš mnoho přesměrování',
        'rate_limited' => 'Příliš mnoho kontrol. Zkuste to za :minutes min',
        'disabled' => 'Služba je dočasně nedostupná',
        'generic' => 'Něco se pokazilo. Zkuste to znovu',
        'unsupported_type' => 'Na této adrese není HTML stránka',
    ],
    'scale' => [
        'not_ready' => 'Nepřipraven',
        'partly' => 'Částečně',
        'almost' => 'Téměř',
        'ready' => 'Připraven',
    ],
];
