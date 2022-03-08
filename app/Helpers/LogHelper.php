<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Class LogHelper - Helper to log errors in needle format
 * @package App\Helpers
 */
class LogHelper
{
    /**
     * @param \Throwable $e
     * @param array|null $params
     */
    public static function error(\Throwable $e, ?array $params = null): void
    {
        \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'params' => $params]);
    }
}
