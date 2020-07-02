<?php

namespace App\Controller\Admin;

use App\Enums\HttpStatusCodes;
use App\Repository\ProjectRepository;
use App\Responses\Project\CreateResponse;
use App\Responses\Project\DetailResponse;
use App\Responses\Project\ListResponse;
use App\Responses\Project\UpdateResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProjectController
 * @package App\Controller\Admin
 *
 * @Route("/project")
 */
class ProjectController extends AbstractBaseController
{
    /**
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param ListResponse $listResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/list", methods={"GET"})
     */
    public function list(Request $request, ProjectRepository $projectRepository, ListResponse $listResponse)
    {
        try {
            $list = $projectRepository->list();
            return $this->jsonResponseService->successResponse($listResponse->handle($list));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param CreateResponse $createResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/create", methods={"POST"})
     */
    public function create(Request $request, ProjectRepository $projectRepository, CreateResponse $createResponse)
    {
        try {
            $projectRepository->checkByName($request->request->get('name'));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        try {
            $projectRepository->checkByListenerUrl($request->request->get('listener_url'));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        try {
            $project = $projectRepository->create($request);
            return $this->jsonResponseService->successResponse($createResponse->response($project));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param DetailResponse $detailResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/detail", methods={"GET"})
     */
    public function detail(Request $request, ProjectRepository $projectRepository, DetailResponse $detailResponse)
    {
        $projectId = $request->request->get('projectId');
        try {
            $detail = $projectRepository->getById($projectId);
            return $this->jsonResponseService->successResponse($detailResponse->response($detail));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param UpdateResponse $updateResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/update", methods={"POST"})
     */
    public function update(Request $request, ProjectRepository $projectRepository, UpdateResponse $updateResponse)
    {
        $projectId = $request->request->get('projectId');
        try {
            $detail = $projectRepository->getById($projectId);
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        try {
            $projectRepository->checkByName($request->request->get('name'));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        try {
            $projectRepository->checkByListenerUrl($request->request->get('listener_url'));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        try {
            $project = $projectRepository->update($detail, $request);
            return $this->jsonResponseService->successResponse($updateResponse->response($project));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }
}