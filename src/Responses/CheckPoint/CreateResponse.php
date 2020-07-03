<?php

namespace App\Responses\CheckPoint;

use App\Entity\CheckPoint;

class CreateResponse
{
    public function response(CheckPoint $checkPoint)
    {
        return [
            'id' => $checkPoint->getId(),
            'name' => $checkPoint->getName(),
            'serverPath' => $checkPoint->getServerPath()
        ];
    }
}