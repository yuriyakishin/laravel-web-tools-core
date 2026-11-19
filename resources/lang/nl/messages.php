<?php

declare(strict_types=1);

return [
    'report' => [
        'again' => 'Nieuwe controle',
        'disclaimer' => 'De score volgt de eigen regels van de tool; het is geen officiële certificering, browsergarantie of zoekpositie.',
        'found' => 'Wat er is gevonden',
        'fix' => 'Hoe op te lossen',
        'copy' => 'Kopiëren',
        'copied' => 'Gekopieerd',
        'ruleset' => 'regels :ruleset',
    ],
    'status' => [
        'pass' => 'Geslaagd',
        'warn' => 'Gedeeltelijk',
        'fail' => 'Niet geslaagd',
        'skip' => 'Overgeslagen',
    ],
    'errors' => [
        'invalid_url' => 'Vul het volledige paginaadres in, bijvoorbeeld https://example.com/page',
        'blocked_host' => 'Dit adres kan niet worden gecontroleerd: de pagina moet bereikbaar zijn via internet',
        'unreachable' => 'De site kon niet worden geopend. Controleer het adres',
        'timeout' => 'De site reageerde niet op tijd. Probeer het opnieuw',
        'http_error' => 'De site reageerde met fout :status. Mogelijk is de pagina afgesloten voor bots',
        'too_large' => 'De pagina is te groot om te controleren',
        'too_many_redirects' => 'Te veel omleidingen',
        'rate_limited' => 'Te veel controles. Probeer het over :minutes min opnieuw',
        'disabled' => 'De dienst is tijdelijk niet beschikbaar',
        'generic' => 'Er ging iets mis. Probeer het opnieuw',
        'unsupported_type' => 'Dit adres is geen HTML-pagina',
    ],
    'scale' => [
        'not_ready' => 'Niet klaar',
        'partly' => 'Gedeeltelijk',
        'almost' => 'Bijna',
        'ready' => 'Klaar',
    ],
];
