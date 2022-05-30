<?php

namespace App\Exceptions;

class NoAuthorizateException extends ApplicationException
{
    protected $appCode = 4002;
//
//    public function __construct($code = 403)
//    {
//        parent::__construct($code);
//    }
}
