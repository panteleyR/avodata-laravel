<?php

namespace App\Contracts;

interface UserService
{
    public function setData(\stdClass $user);
    public function get(): ?\stdClass;
}
