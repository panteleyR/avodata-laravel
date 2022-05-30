<?php

namespace App\Events;

use App\Models\TemporaryUser;

class VerifyUserDone
{
    public TemporaryUser $tmpUser;

    public function __construct(TemporaryUser $tmpUser)
    {
        $this->tmpUser = $tmpUser;
    }
}
