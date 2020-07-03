<?php

namespace App\Repository;

use App\Entity\CheckPoint;
use App\Entity\Project;
use App\Enums\CheckPointStatusCodes;
use App\Exceptions\Login\CheckPointCreateException;
use App\Exceptions\Login\CheckPointNotFoundException;
use App\Exceptions\Login\CheckPointUpdateException;
use App\Repository\Interfaces\CheckPointInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method CheckPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheckPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheckPoint[]    findAll()
 * @method CheckPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckPointRepository extends ServiceEntityRepository implements CheckPointInterface
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CheckPoint::class);
    }

    /**
     * @return array
     */
    public function listAll(): array
    {
        return $this->findAll();
    }

    /**
     * @param Project $project
     * @return array
     */
    public function listByProject(Project $project): array
    {
        return $this->findBy(['project' => $project]);
    }

    /**
     * @param $checkPointId
     * @return CheckPoint|null
     * @throws CheckPointNotFoundException
     */
    public function findById($checkPointId): ?CheckPoint
    {
        $checkPoint = $this->find($checkPointId);
        if(!$checkPoint){
            throw new CheckPointNotFoundException();
        }

        return $checkPoint;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return CheckPoint|null
     * @throws CheckPointCreateException
     */
    public function create(Request $request, Project $project): ?CheckPoint
    {
        $checkPoint = new CheckPoint();
        $checkPoint->setProject($project);
        $checkPoint->setName($request->request->get('name'));
        $checkPoint->setServerPath($request->request->get('server_path'));
        $checkPoint->setStatus(CheckPointStatusCodes::ACTIVE_STATUS);
        $checkPoint->setCreatedAt(new \DateTime());

        $entityManager = $this->getEntityManager();
        try {
            $entityManager->persist($checkPoint);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error('CheckPoint Create Exception: ' . $exception->getMessage());
            throw new CheckPointCreateException();
        }

        return $checkPoint;
    }

    /**
     * @param CheckPoint $checkPoint
     * @param Request $request
     * @return CheckPoint|null
     * @throws CheckPointUpdateException
     */
    public function update(CheckPoint $checkPoint, Request $request): ?CheckPoint
    {
        if (!empty($request->request->get('name'))) {
            $checkPoint->setName($request->request->get('name'));
        }
        $checkPoint->setStatus($request->request->get('status') ?? true);
        $checkPoint->setUpdatedAt(new \DateTime());

        $entityManager = $this->getEntityManager();
        try {
            $entityManager->persist($checkPoint);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error('CheckPoint Update Exception: ' . $exception->getMessage());
            throw new CheckPointUpdateException();
        }

        return $checkPoint;
    }
}
