<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tamanho máximo de arquivo de log (bytes)
    |--------------------------------------------------------------------------
    |
    | Arquivos maiores que este valor não são carregados na memória de uma vez.
    | O padrão de 48 MB deixa margem para a leitura do arquivo completo, além
    | do overhead de serialização da resposta Inertia, sem encostar no
    | memory_limit de 512 MB definido em docker/php.ini.
    |
    */
    'max_file_bytes' => (int) env('SYSTEM_LOG_MAX_FILE_BYTES', 48 * 1024 * 1024),
];
