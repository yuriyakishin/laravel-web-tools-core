<?php

declare(strict_types=1);

return [
    'report' => [
        'again' => 'Nueva comprobación',
        'disclaimer' => 'La puntuación sigue las reglas propias de la herramienta; no es una certificación oficial, una garantía del navegador ni una posición en buscadores.',
        'found' => 'Qué se encontró',
        'fix' => 'Cómo corregirlo',
        'copy' => 'Copiar',
        'copied' => 'Copiado',
        'ruleset' => 'reglas :ruleset',
    ],
    'status' => [
        'pass' => 'Superado',
        'warn' => 'Parcial',
        'fail' => 'No superado',
        'skip' => 'Omitido',
    ],
    'errors' => [
        'invalid_url' => 'Introduzca la dirección completa de la página, por ejemplo https://example.com/page',
        'blocked_host' => 'Esta dirección no se puede comprobar: la página debe ser accesible desde internet',
        'unreachable' => 'No se pudo abrir el sitio. Compruebe la dirección',
        'timeout' => 'El sitio no respondió a tiempo. Inténtelo de nuevo',
        'http_error' => 'El sitio respondió con el error :status. Puede que la página esté cerrada a los bots',
        'too_large' => 'La página es demasiado grande para comprobarla',
        'too_many_redirects' => 'Demasiadas redirecciones',
        'rate_limited' => 'Demasiadas comprobaciones. Inténtelo dentro de :minutes min',
        'disabled' => 'El servicio no está disponible temporalmente',
        'generic' => 'Algo salió mal. Inténtelo de nuevo',
        'unsupported_type' => 'Esta dirección no es una página HTML',
    ],
    'scale' => [
        'not_ready' => 'No preparado',
        'partly' => 'Parcial',
        'almost' => 'Casi',
        'ready' => 'Preparado',
    ],
];
