<?php

namespace App\Responses\Project;

use App\Entity\Project;

class DetailResponse
{
    public function response(Project $project)
    {
        return [
            'id' => $project->getId(),
            'name' => $project->getName(),
        ];
    }
}