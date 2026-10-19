<?php

namespace App\Logging;

use Illuminate\Log\Logger as IlluminateLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Utils;
use ReflectionProperty;

final class ElipseQuinzenaTap
{
    public function __invoke($logger): void
    {
        $monolog = $logger instanceof IlluminateLogger
            ? $logger->getLogger()
            : $logger;

        if (! $monolog instanceof MonologLogger) {
            return;
        }

        $path = Utils::canonicalizePath($this->resolveQuinzenaPath());

        foreach ($monolog->getHandlers() as $handler) {
            if ($handler instanceof StreamHandler) {
                $this->retargetStreamForAppend($handler, $path);
                $handler->setFormatter(new ElipseAuditJsonFormatter());
            }
        }
    }

    /**
     * Monolog 3 não expõe setUrl(). Trocar o arquivo sem substituir o handler (setHandlers pode quebrar o canal em alguns ambientes).
     * fileOpenMode explícito em 'a' evita truncagem se o modo tiver sido alterado.
     */
    private function retargetStreamForAppend(StreamHandler $handler, string $canonicalPath): void
    {
        $handler->close();

        $url = new ReflectionProperty(StreamHandler::class, 'url');
        $url->setAccessible(true);
        $url->setValue($handler, $canonicalPath);

        $mode = new ReflectionProperty(StreamHandler::class, 'fileOpenMode');
        $mode->setAccessible(true);
        $mode->setValue($handler, 'a');

        foreach (['dirCreated', 'inodeUrl', 'retrying', 'errorMessage'] as $privateProperty) {
            $property = new ReflectionProperty(StreamHandler::class, $privateProperty);
            $property->setAccessible(true);
            $default = $privateProperty === 'retrying' ? false : null;
            $property->setValue($handler, $default);
        }
    }

    private function resolveQuinzenaPath(): string
    {
        $year = date('Y');
        $month = date('m');
        $day = (int) date('j');

        $half = $day <= 15 ? 1 : 2;

        return storage_path("logs/elipse_logs_{$year}_{$month}_{$half}.log");
    }
}
