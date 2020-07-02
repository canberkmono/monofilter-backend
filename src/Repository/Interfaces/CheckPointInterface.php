<?php

namespace App\Repository\Interfaces;

use App\Entity\CheckPoint;
use Symfony\Component\HttpFoundation\Request;

interface CheckPointInterface
{
    public function listAll(): array;

    public function listByProjectId($projectId): array;

    public function findById($checkPointId): ?CheckPoint;

    public function checkByPath($serverPath): ?CheckPoint;

    public function create(Request $request): ?CheckPoint;

    public function update(CheckPoint $checkPoint, Request $request): ?CheckPoint;
}