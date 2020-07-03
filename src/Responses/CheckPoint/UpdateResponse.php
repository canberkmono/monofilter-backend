<?php

namespace App\Responses\CheckPoint;

use App\Entity\CheckPoint;

class UpdateResponse
{
    public function response(CheckPoint $checkPoint)
    {
        return [
            'id' => $checkPoint->getId(),
            'name' => $checkPoint->getName(),
        ];
    }
}