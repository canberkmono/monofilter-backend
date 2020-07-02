<?php

namespace App\Repository\Interfaces;

use App\Entity\Project;
use Symfony\Component\HttpFoundation\Request;

interface ProjectInterface
{
    public function list(): array;

    public function checkByName($projectName): ?Project;

    public function checkByListenerUrl($listenerUrl): ?Project;

    public function create(Request $request): ?Project;

    public function getById($projectId): ?Project;

    public function getByProjectToken($token): ?Project;

    public function update(Project $project, Request $request): ?Project;

}