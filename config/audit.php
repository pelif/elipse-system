<?php

return [

    /*
    | Operação do worker (Docker em dev vs host em homolog/prod): ver
    | specs/operations/audit_queue_worker.md
    */

    /*
    |--------------------------------------------------------------------------
    | Canal de log Monolog
    |--------------------------------------------------------------------------
    */
    'channel' => env('AUDIT_LOG_CHANNEL', 'elipse_audit'),

    /*
    |--------------------------------------------------------------------------
    | Fila para jobs de auditoria
    |--------------------------------------------------------------------------
    */
    'queue' => env('AUDIT_QUEUE', 'audit'),

    /*
    |--------------------------------------------------------------------------
    | Lista negra de nomes de atributo (merge com defaults da factory)
    |--------------------------------------------------------------------------
    */
    'sensitive_keys' => [
        // 'two_factor_secret',
    ],

    /*
    |--------------------------------------------------------------------------
    | Padrões glob (fnmatch) para nomes de chave sensíveis
    |--------------------------------------------------------------------------
    */
    'sensitive_key_patterns' => [
        '*_secret',
        '*_password',
        '*_token',
        '*_api_key',
    ],

    /*
    |--------------------------------------------------------------------------
    | Whitelist opcional de atributos (vazio = todos exceto blacklist)
    | Se preenchido, só estas chaves entram em attributes/changes.
    |--------------------------------------------------------------------------
    */
    'attribute_whitelist' => [],

    /*
    |--------------------------------------------------------------------------
    | Limites de tamanho
    |--------------------------------------------------------------------------
    */
    'max_string_length' => (int) env('AUDIT_MAX_STRING_LENGTH', 2000),

    'max_array_depth' => (int) env('AUDIT_MAX_ARRAY_DEPTH', 5),

    /** Máximo de chaves por nível em arrays aninhados (JSON/casts). */
    'max_array_keys_per_level' => (int) env('AUDIT_MAX_ARRAY_KEYS', 50),

    /** Máximo de chaves de coluna em created/deleted/updated. */
    'max_attribute_keys' => (int) env('AUDIT_MAX_ATTRIBUTE_KEYS', 150),

    /** User-Agent e campos longos do contexto HTTP. */
    'max_request_user_agent_length' => (int) env('AUDIT_MAX_UA_LENGTH', 500),

    /**
     * Se o JSON do payload exceder este tamanho (bytes), o job grava um resumo
     * mínimo em vez do corpo completo (evita linhas gigantes no arquivo).
     */
    'max_payload_json_bytes' => (int) env('AUDIT_MAX_PAYLOAD_JSON_BYTES', 65536),

    /*
    |--------------------------------------------------------------------------
    | Modelos / tabelas ignorados (sem evento audit.payload)
    |--------------------------------------------------------------------------
    */
    'ignored_models' => [
        // \App\Models\SomeInternalModel::class,
    ],

    'ignored_tables' => [
        // 'password_reset_tokens',
    ],

];
