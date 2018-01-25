<?php

namespace App\Services;

class CookieManagerService
{
    public static function add($key, $value, $time = 0, $path = '/')
    {
        setcookie($key, $value, $time, $path);
    }

    public static function get($key)
    {
        return $_COOKIE[$key];
    }

    public static function remove($key, $path = '/')
    {
        setcookie($key, '', time() - 3600, $path);
    }
}
