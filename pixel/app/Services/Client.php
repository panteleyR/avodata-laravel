<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Contracts\Client as ClientContract;

class Client implements ClientContract
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function ip(): string
    {
        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ip = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ip = $_SERVER['REMOTE_ADDR'];

        return $ip;
    }

    public function frontCookie(string $key): ?string
    {
        $cookie = $this->request->input('cookie');
        $cookie = explode(';', $cookie);
        if (!is_array($cookie) || $cookie[0] === '') {
            return null;
        }

        $cookieEnd = [];
        foreach ($cookie as $val) {
            $cookieData = explode('=', $val);
            $cookieEnd[trim($cookieData[0])] = trim($cookieData[1]);
        }

        return array_key_exists($key,$cookieEnd) ? $cookieEnd[$key] : null;
    }

    public function backCookie(string $key): ?string
    {
        return $this->request->cookie($key);
    }

    public function setCookie(string $key, string $value)
    {
        setcookie($key, $value, ["path" => "*", "expires" => time()+(60*60*24*30*12*10), "samesite" => "Lax", "secure" => false]);
    }
}
