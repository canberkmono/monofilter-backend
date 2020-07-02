<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 28.06.2020
 * Time: 16:17
 */

namespace App\Responses\User;

use App\Entity\User;

class CreateResponse
{
    public function response(User $user)
    {
        return [
            'id' => $user->getId(),
            'fullName' => $user->getFullName(),
            'email' => $user->getEmail()
        ];
    }
}