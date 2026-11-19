<?php

return [
    'report' => [
        'again' => 'Neue Prüfung',
        'disclaimer' => 'Die Bewertung folgt den eigenen Regeln des Tools; sie ist keine offizielle Zertifizierung, keine Browser-Garantie und kein Suchranking.',
        'found' => 'Was gefunden wurde',
        'fix' => 'So beheben Sie es',
        'copy' => 'Kopieren',
        'copied' => 'Kopiert',
        'ruleset' => 'Regelwerk :ruleset',
    ],
    'status' => [
        'pass' => 'Bestanden',
        'warn' => 'Teilweise',
        'fail' => 'Nicht bestanden',
        'skip' => 'Übersprungen',
    ],
    'errors' => [
        'invalid_url' => 'Geben Sie die vollständige Seitenadresse ein, z. B. https://example.com/page',
        'blocked_host' => 'Diese Adresse kann nicht geprüft werden: Die Seite muss aus dem Internet erreichbar sein',
        'unreachable' => 'Die Website konnte nicht geöffnet werden. Prüfen Sie die Adresse',
        'timeout' => 'Die Website hat nicht rechtzeitig geantwortet. Versuchen Sie es erneut',
        'http_error' => 'Die Website hat mit Fehler :status geantwortet. Die Seite ist möglicherweise für Bots gesperrt',
        'too_large' => 'Die Seite ist zu groß für die Prüfung',
        'too_many_redirects' => 'Zu viele Weiterleitungen',
        'rate_limited' => 'Zu viele Prüfungen. Versuchen Sie es in :minutes Min. erneut',
        'disabled' => 'Der Dienst ist vorübergehend nicht verfügbar',
        'generic' => 'Etwas ist schiefgelaufen. Versuchen Sie es erneut',
        'unsupported_type' => 'Unter dieser Adresse befindet sich keine HTML-Seite',
    ],
    'scale' => [
        'not_ready' => 'Nicht bereit',
        'partly' => 'Teilweise',
        'almost' => 'Fast',
        'ready' => 'Bereit',
    ],
];
