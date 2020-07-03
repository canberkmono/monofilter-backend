<?php

namespace App\Responses\CheckPoint;

use App\Entity\CheckPoint;

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

    private function fillData(CheckPoint $checkPoint)
    {
        return [
            'id' => $checkPoint->getId(),
            'name' => $checkPoint->getName(),
        ];
    }
}