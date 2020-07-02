<?php

namespace App\Responses\Project;

use App\Entity\Project;

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

    private function fillData(Project $project)
    {
        return [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'listenerUrl' => $project->getListenerUrl(),
            'projectToken' => $project->getProjectToken()
        ];
    }
}