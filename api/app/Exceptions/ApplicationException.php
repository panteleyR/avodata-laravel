<?php

namespace App\Exceptions;

use \Exception;

class ApplicationException extends Exception
{
    protected $appCode = 400;

    public function __construct($code = 400, Exception $previous = null) {
        parent::__construct('Application error', $code, $previous);
    }

    // Переопределим строковое представление объекта.
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getApplicationCode() {
        return $this->appCode;
    }
}
