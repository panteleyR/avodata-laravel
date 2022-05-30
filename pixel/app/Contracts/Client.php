<?php

namespace App\Contracts;

interface Client
{
    public function ip(): string;
    public function frontCookie(string $key): ?string;
    public function backCookie(string $key): ?string;
    public function setCookie(string $key, string $value);
}
