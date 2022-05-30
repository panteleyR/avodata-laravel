<?php

namespace App\Services;

use App\Contracts\Confirm as ConfirmContract;
use App\Exceptions\InvalidCreditionalException;
use App\Events\ConfirmCodeSet;
use Illuminate\Support\Facades\Redis;

class Confirm implements ConfirmContract
{
    private static int $timeLive = 300;

    public static function sendCode(string $type, $user)
    {
        $code = static::generateCode();
        Redis::setex($type, static::$timeLive, $code);

        event(new ConfirmCodeSet($code, $user->email));
    }

    public static function checkCode(string $type, string $tryCode)
    {
        $code = Redis::get($type);
        if ($tryCode !== $code) {
            throw new InvalidCreditionalException();
        }
    }

    public static function generateCode(): string
    {
        return (string) random_int(100000, 999999);
    }
}
