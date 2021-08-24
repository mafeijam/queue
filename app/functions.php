<?php

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

if (!function_exists('create_logger_config')) {
    function create_logger_config($level, $path) {
        return [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => storage_path($path),
                'level' => $level,
                'bubble' => false
            ],
            'formatter' => LineFormatter::class,
            'formatter_with' => [
                'dateFormat' => 'Y-m-d H:i:s P',
                'ignoreEmptyContextAndExtra' => true
            ]
        ];
    }

}
