<?php

namespace App\Repository;

use App\Entity\CheckPoint;
use App\Repository\Interfaces\CheckPointInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method CheckPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheckPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheckPoint[]    findAll()
 * @method CheckPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckPointRepository extends ServiceEntityRepository implements CheckPointInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CheckPoint::class);
    }


    public function listAll(): array
    {
        // TODO: Implement listAll() method.
    }

    public function listByProjectId($projectId): array
    {
        // TODO: Implement listByProjectId() method.
    }

    public function findById($checkPointId): ?CheckPoint
    {
        // TODO: Implement findById() method.
    }

    public function checkByPath($serverPath): ?CheckPoint
    {
        // TODO: Implement checkByPath() method.
    }

    public function create(Request $request): ?CheckPoint
    {
        // TODO: Implement create() method.
    }

    public function update(CheckPoint $checkPoint, Request $request): ?CheckPoint
    {
        // TODO: Implement update() method.
    }
}
