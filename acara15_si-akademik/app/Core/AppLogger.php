<?php

namespace App\Core;

use Throwable;

class AppLogger
{
    public static function error(string $context, Throwable $exception): void
    {
        $entry = sprintf(
            "[%s] %s: %s: %s%s",
            date('Y-m-d H:i:s'),
            $context,
            $exception::class,
            $exception->getMessage(),
            PHP_EOL
        );
        $directory = dirname(__DIR__, 2) . '/storage/logs';

        if (!is_dir($directory) && !@mkdir($directory, 0770, true) && !is_dir($directory)) {
            error_log('Gagal membuat direktori log aplikasi. ' . $entry);

            return;
        }

        if (!@error_log($entry, 3, $directory . '/app.log')) {
            error_log('Gagal menulis storage/logs/app.log. ' . $entry);
        }
    }
}
