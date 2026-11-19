<?php

declare(strict_types=1);

return [
    'report' => [
        'again' => 'Nuova verifica',
        'disclaimer' => 'Il punteggio segue le regole proprie dello strumento; non è una certificazione ufficiale, una garanzia del browser né un posizionamento nella ricerca.',
        'found' => 'Cosa è stato trovato',
        'fix' => 'Come correggere',
        'copy' => 'Copia',
        'copied' => 'Copiato',
        'ruleset' => 'regole :ruleset',
    ],
    'status' => [
        'pass' => 'Superato',
        'warn' => 'Parziale',
        'fail' => 'Non superato',
        'skip' => 'Saltato',
    ],
    'errors' => [
        'invalid_url' => 'Inserisci l’indirizzo completo della pagina, ad esempio https://example.com/page',
        'blocked_host' => 'Questo indirizzo non può essere verificato: la pagina deve essere raggiungibile da internet',
        'unreachable' => 'Impossibile aprire il sito. Controlla l’indirizzo',
        'timeout' => 'Il sito non ha risposto in tempo. Riprova',
        'http_error' => 'Il sito ha risposto con l’errore :status. Forse la pagina è chiusa ai bot',
        'too_large' => 'La pagina è troppo grande per la verifica',
        'too_many_redirects' => 'Troppi reindirizzamenti',
        'rate_limited' => 'Troppe verifiche. Riprova tra :minutes min',
        'disabled' => 'Il servizio non è temporaneamente disponibile',
        'generic' => 'Qualcosa è andato storto. Riprova',
        'unsupported_type' => 'Questo indirizzo non è una pagina HTML',
    ],
    'scale' => [
        'not_ready' => 'Non pronto',
        'partly' => 'Parziale',
        'almost' => 'Quasi',
        'ready' => 'Pronto',
    ],
];
