<?php

namespace App\Repository\Interfaces;

use App\Entity\CheckPoint;
use App\Entity\Project;
use Symfony\Component\HttpFoundation\Request;

interface CheckPointInterface
{
    public function listAll(): array;

    public function listByProject(Project $project): array;

    public function findById($checkPointId): ?CheckPoint;

    public function create(Request $request, Project $project): ?CheckPoint;

    public function update(CheckPoint $checkPoint, Request $request): ?CheckPoint;
}