<?php

namespace App\Events;

class ConfirmCodeSet
{
    public string $code;
    public string $email;

    public function __construct(string $code, string $email)
    {
        $this->code = $code;
        $this->email = $email;
    }
}
