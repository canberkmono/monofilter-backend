<?php

namespace App\Responses\User;

use App\Entity\User;

class DetailResponse
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