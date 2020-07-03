<?php

namespace App\Controller\Admin;

use App\Enums\HttpStatusCodes;
use App\Repository\CheckPointRepository;
use App\Repository\ProjectRepository;
use App\Responses\CheckPoint\CreateResponse;
use App\Responses\CheckPoint\DetailResponse;
use App\Responses\CheckPoint\ListResponse;
use App\Responses\CheckPoint\UpdateResponse;
use App\Service\JsonResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CheckPointController
 * @package App\Controller\Admin
 *
 * @Route("/checkPoint")
 */
class CheckPointController extends AbstractBaseController
{
    CONST UPLOAD_CHECK_POINT_PATH = '';

    private $appKernel;

    public function __construct(JsonResponseService $jsonResponseService, LoggerInterface $loggerService, KernelInterface $appKernel)
    {
        parent::__construct($jsonResponseService, $loggerService);
        $this->appKernel = $appKernel;
    }

    /**
     * @param Request $request
     * @param CheckPointRepository $checkPointRepository
     * @param ProjectRepository $projectRepository
     * @param ListResponse $listResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/list", methods={"GET"})
     */
    public function list(Request $request, CheckPointRepository $checkPointRepository, ProjectRepository $projectRepository, ListResponse $listResponse)
    {
        try {
            $projectId = $request->request->get('projectId');
            $list = $checkPointRepository->listAll();
            if (isset($projectId)) {
                $findProject = $projectRepository->getById($projectId);
                $list = $checkPointRepository->listByProject($findProject);
            }
            return $this->jsonResponseService->successResponse($listResponse->handle($list));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param CheckPointRepository $checkPointRepository
     * @param CreateResponse $createResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/create", methods={"POST"})
     */
    public function create(Request $request, CheckPointRepository $checkPointRepository, CreateResponse $createResponse)
    {
        try {
            $zipFile = $request->files->get('zipFile');
        } catch (\Exception $exception) {
            return $this->jsonResponseService->successResponse([]);
        }
    }

    /**
     * @param Request $request
     * @param CheckPointRepository $checkPointRepository
     * @param DetailResponse $detailResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/detail", methods={"GET"})
     */
    public function detail(Request $request, CheckPointRepository $checkPointRepository, DetailResponse $detailResponse)
    {
        $checkPointId = $request->request->get('checkPointId');
        try {
            $detail = $checkPointRepository->findById($checkPointId);
            return $this->jsonResponseService->successResponse($detailResponse->response($detail));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param CheckPointRepository $checkPointRepository
     * @param UpdateResponse $updateResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/update", methods={"POST"})
     */
    public function update(Request $request, CheckPointRepository $checkPointRepository, UpdateResponse $updateResponse)
    {
        $checkPointId = $request->request->get('checkPointId');
        try {
            $detail = $checkPointRepository->findById($checkPointId);
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        try {
            $updateCheckPoint = $checkPointRepository->update($detail, $request);
            return $this->jsonResponseService->successResponse($updateResponse->response($updateCheckPoint));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }
}