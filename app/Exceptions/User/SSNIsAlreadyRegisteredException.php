<?php

declare(strict_types=1);

namespace App\Exceptions\User;

use Throwable;

class SSNIsAlreadyRegisteredException extends \DomainException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = 'User with this SSN is already registered.';
        }
        parent::__construct($message, $code, $previous);
    }
}
