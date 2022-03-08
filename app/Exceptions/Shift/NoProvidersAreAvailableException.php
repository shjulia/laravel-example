<?php

namespace App\Exceptions\Shift;

use Throwable;

class NoProvidersAreAvailableException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = 'No providers are available. We will refund your charge soon.';
        }
        parent::__construct($message, $code, $previous);
    }
}
