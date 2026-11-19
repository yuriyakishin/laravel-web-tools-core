<?php

declare(strict_types=1);

return [
    'report' => [
        'again' => 'Nouvelle vérification',
        'disclaimer' => 'Le score suit les règles propres de l’outil ; ce n’est ni une certification officielle, ni une garantie du navigateur, ni un classement dans la recherche.',
        'found' => 'Ce qui a été trouvé',
        'fix' => 'Comment corriger',
        'copy' => 'Copier',
        'copied' => 'Copié',
        'ruleset' => 'règles :ruleset',
    ],
    'status' => [
        'pass' => 'Réussi',
        'warn' => 'Partiel',
        'fail' => 'Échoué',
        'skip' => 'Ignoré',
    ],
    'errors' => [
        'invalid_url' => 'Saisissez l’adresse complète de la page, par exemple https://example.com/page',
        'blocked_host' => 'Cette adresse ne peut pas être vérifiée : la page doit être accessible depuis Internet',
        'unreachable' => 'Impossible d’ouvrir le site. Vérifiez l’adresse',
        'timeout' => 'Le site n’a pas répondu à temps. Réessayez',
        'http_error' => 'Le site a répondu par l’erreur :status. La page est peut-être fermée aux robots',
        'too_large' => 'La page est trop volumineuse pour être vérifiée',
        'too_many_redirects' => 'Trop de redirections',
        'rate_limited' => 'Trop de vérifications. Réessayez dans :minutes min',
        'disabled' => 'Le service est temporairement indisponible',
        'generic' => 'Une erreur s’est produite. Réessayez',
        'unsupported_type' => 'Cette adresse n’est pas une page HTML',
    ],
    'scale' => [
        'not_ready' => 'Pas prêt',
        'partly' => 'Partiel',
        'almost' => 'Presque',
        'ready' => 'Prêt',
    ],
];
