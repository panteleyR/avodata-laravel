<?php

namespace App\Services;

use App\Contracts\UserService as UserServiceContract;

class UserService implements UserServiceContract
{
    private \stdClass $user;

    public function setData(\stdClass $user)
    {
        $this->user = $user;
    }

    public function get(): ?\stdClass
    {
        return $this->user;
    }
}
