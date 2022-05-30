<?php

namespace App\Exceptions;

class InvalidCreditionalException extends ApplicationException
{
    protected $appCode = 4001;

    public function __construct($code = 422)
    {
        parent::__construct($code);
    }
}
