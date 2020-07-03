<?php

namespace App\Repository;

use App\Entity\Project;
use App\Enums\ProjectStatusCodes;
use App\Exceptions\Project\ProjectAlreadyExistsException;
use App\Exceptions\Project\ProjectCreateException;
use App\Exceptions\Project\ProjectNotFoundException;
use App\Exceptions\Project\ProjectUpdateException;
use App\Helpers\TokenHelper;
use App\Repository\Interfaces\ProjectInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository implements ProjectInterface
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->findAll();
    }

    /**
     * @param $name
     * @return Project|null
     * @throws ProjectAlreadyExistsException
     */
    public function checkByName($name): ?Project
    {
        $project = $this->findOneBy(['name' => $name, 'status' => ProjectStatusCodes::ACTIVE_STATUS]);
        if ($project) {
            throw new ProjectAlreadyExistsException('Project Name Already Used');
        }
        return $project;
    }

    /**
     * @param $listenerUrl
     * @return Project|null
     * @throws ProjectAlreadyExistsException
     */
    public function checkByListenerUrl($listenerUrl): ?Project
    {
        $project = $this->findOneBy(['listener_url' => $listenerUrl, 'status' => ProjectStatusCodes::ACTIVE_STATUS]);
        if ($project) {
            throw new ProjectAlreadyExistsException('Project Listener Url Already Used');
        }
        return $project;
    }

    /**
     * @param Request $request
     * @return Project|null
     * @throws ProjectCreateException
     */
    public function create(Request $request): ?Project
    {
        $project = new Project();
        $project->setName($request->request->get('name'));
        $project->setProjectToken(TokenHelper::generateApiToken());
        $project->setListenerUrl($request->request->get('listener_url'));
        $project->setStatus(ProjectStatusCodes::ACTIVE_STATUS);
        $project->setCreatedAt(new \DateTime());

        $entityManager = $this->getEntityManager();
        try {
            $entityManager->persist($project);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error('Project Create Exception: ' . $exception->getMessage());
            throw new ProjectCreateException();
        }

        return $project;
    }

    /**
     * @param $projectId
     * @return Project|null
     * @throws ProjectNotFoundException
     */
    public function getById($projectId): ?Project
    {
        $project = $this->find($projectId);
        if (!$project) {
            throw new ProjectNotFoundException();
        }

        return $project;
    }

    /**
     * @param $token
     * @return Project|null
     * @throws ProjectNotFoundException
     */
    public function getByProjectToken($token): ?Project
    {
        $project = $this->findOneBy(['project_token' => $token, 'status' => ProjectStatusCodes::ACTIVE_STATUS]);
        if (!$project) {
            throw new ProjectNotFoundException();
        }

        return $project;
    }

    /**
     * @param Project $project
     * @param Request $request
     * @return Project|null
     * @throws ProjectUpdateException
     */
    public function update(Project $project, Request $request): ?Project
    {
        if (!empty($request->request->get('name'))) {
            $project->setName($request->request->get('name'));
        }
        if (!empty($request->request->get('listener_url'))) {
            $project->setListenerUrl($request->request->get('listener_url'));
        }
        if (!empty($request->request->get('project_token'))) {
            $project->setProjectToken($request->request->get('project_token'));
        }

        $project->setStatus($request->request->get('status') ?? true);
        $project->setUpdatedAt(new \DateTime());
        $entityManager = $this->getEntityManager();
        try {
            $entityManager->persist($project);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error('Project Update Exception: ' . $exception->getMessage());
            throw new ProjectUpdateException();
        }
        return $project;
    }
}
