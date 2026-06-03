<?php

namespace App\Exceptions;

use Exception;
use App\Models\User;

class LeadAlreadyProcessedException extends Exception
{
    protected User $user;

    public function __construct(User $user, $message = "Lead already processed by parallel process.", $code = 0, \Throwable $previous = null)
    {
        $this->user = $user;
        parent::__construct($message, $code, $previous);
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
