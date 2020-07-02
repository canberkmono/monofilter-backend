<?php

namespace App\Helpers;

final class TokenHelper
{
    public static function generateToken(): string
    {
        $bytes = openssl_random_pseudo_bytes(16);
        return bin2hex($bytes);
    }

    public static function generateApiToken(): string
    {
        $bytes = openssl_random_pseudo_bytes(32);
        return bin2hex($bytes);
    }
}