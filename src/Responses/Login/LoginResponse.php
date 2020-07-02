<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 28.06.2020
 * Time: 16:22
 */

namespace App\Responses\Login;

use App\Entity\User;

class LoginResponse
{
    public function response(User $user)
    {
        return [
            'id' => $user->getId(),
            'fullName' => $user->getFullName(),
            'email' => $user->getEmail(),
            'token' => $user->getToken()
        ];
    }
}