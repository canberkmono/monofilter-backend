<?php

namespace App\Responses\CheckPoint;

use App\Entity\CheckPoint;

class DetailResponse
{
    public function response(CheckPoint $checkPoint)
    {
        return [
            'id' => $checkPoint->getId(),
            'name' => $checkPoint->getName(),
        ];
    }
}