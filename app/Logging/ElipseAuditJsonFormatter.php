<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

final class ElipseAuditJsonFormatter extends JsonFormatter
{
    public function __construct()
    {
        parent::__construct(JsonFormatter::BATCH_MODE_JSON, false);
    }

    public function format(LogRecord $record): string
    {
        return parent::format($record) . PHP_EOL . PHP_EOL;
    }
}
