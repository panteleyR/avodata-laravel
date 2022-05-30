<?php

namespace App\Contracts;

interface Confirm
{
    public static function sendCode(string $type, $user);
    public static function checkCode(string $type, string $code);
    public static function generateCode(): string;
}
