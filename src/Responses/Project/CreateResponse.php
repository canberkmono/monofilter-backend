<?php

namespace App\Responses\Project;

use App\Entity\Project;

class CreateResponse
{
    public function response(Project $project)
    {
        return [
            'id' => $project->getId(),
            'name' => $project->getName(),
        ];
    }
}