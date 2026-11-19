<?php

return [
    'report' => [
        'again' => 'Nova verificação',
        'disclaimer' => 'A pontuação segue as regras próprias da ferramenta; não é uma certificação oficial, uma garantia do navegador nem uma posição nas pesquisas.',
        'found' => 'O que foi encontrado',
        'fix' => 'Como corrigir',
        'copy' => 'Copiar',
        'copied' => 'Copiado',
        'ruleset' => 'regras :ruleset',
    ],
    'status' => [
        'pass' => 'Aprovado',
        'warn' => 'Parcial',
        'fail' => 'Reprovado',
        'skip' => 'Ignorado',
    ],
    'errors' => [
        'invalid_url' => 'Indique o endereço completo da página, por exemplo https://example.com/page',
        'blocked_host' => 'Este endereço não pode ser verificado: a página tem de estar acessível a partir da internet',
        'unreachable' => 'Não foi possível abrir o site. Verifique o endereço',
        'timeout' => 'O site não respondeu a tempo. Tente novamente',
        'http_error' => 'O site respondeu com o erro :status. A página pode estar fechada a bots',
        'too_large' => 'A página é demasiado grande para verificar',
        'too_many_redirects' => 'Demasiados redirecionamentos',
        'rate_limited' => 'Demasiadas verificações. Tente dentro de :minutes min',
        'disabled' => 'O serviço está temporariamente indisponível',
        'generic' => 'Algo correu mal. Tente novamente',
        'unsupported_type' => 'Este endereço não é uma página HTML',
    ],
    'scale' => [
        'not_ready' => 'Não preparado',
        'partly' => 'Parcial',
        'almost' => 'Quase',
        'ready' => 'Preparado',
    ],
];
