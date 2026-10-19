<?php

declare(strict_types=1);

/*
| Garante que `bootstrap/providers.php` seja mesclado em `app.providers`.
| Com `bootstrap/cache/config.php` presente, o Laravel pula o merge e providers
| adicionados só em `bootstrap/providers.php` (ex.: AuditServiceProvider) não carregam.
*/

$baseDir = dirname(__DIR__);
$configCache = $baseDir . '/bootstrap/cache/config.php';
if (is_file($configCache)) {
    unlink($configCache);
}

require $baseDir . '/vendor/autoload.php';
