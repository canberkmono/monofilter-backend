<?php

namespace App\Responses\Project;

use App\Entity\Project;

class UpdateResponse
{
    public function response(Project $project)
    {
        return [
            'id' => $project->getId(),
            'name' => $project->getName(),
        ];
    }
}