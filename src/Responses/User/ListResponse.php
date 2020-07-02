<?php

namespace App\Responses\User;

use App\Entity\User;

class ListResponse
{
    public function handle($data)
    {
        if (is_object($data)) {
            $response = $this->fillData($data);
        } else {
            $response = [];
            foreach ($data as $item) {
                $response[] = $this->fillData($item);
            }
        }
        return $response;
    }

    private function fillData(User $user)
    {
        return [
            'id' => $user->getId(),
            'full_name' => $user->getFullName(),
            'email' => $user->getEmail(),
            'status' => $user->getStatus(),
            'roles' => $user->getRoles(),
            'created_at' => $user->getCreatedAt(),
            'updated_at' => $user->getUpdatedAt(),
        ];
    }
}