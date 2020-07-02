<?php

namespace App\Controller\Admin;

use App\Enums\HttpStatusCodes;
use App\Exceptions\User\UserCreateException;
use App\Exceptions\User\UserNotValidEmailException;
use App\Helpers\TokenHelper;
use App\Repository\UserRepository;
use App\Responses\User\CreateResponse;
use App\Responses\User\DetailResponse;
use App\Responses\User\ListResponse;
use App\Responses\User\UpdateResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller\Admin
 *
 * @Route("/user")
 */
class UserController extends AbstractBaseController
{
    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ListResponse $listResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/list", methods={"GET"})
     */
    public function list(Request $request, UserRepository $userRepository, ListResponse $listResponse)
    {
        try {
            $list = $userRepository->list();
            return $this->jsonResponseService->successResponse($listResponse->handle($list));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param CreateResponse $createResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/create", methods={"POST"})
     */
    public function create(Request $request, UserRepository $userRepository, CreateResponse $createResponse)
    {
        try {
            $userRepository->checkEmail($request->request->get('email'));
        } catch (UserNotValidEmailException $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        $request->request->add([
            'token' => TokenHelper::generateToken()
        ]);

        try {
            $createUser = $userRepository->create($request);
        } catch (UserCreateException $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
        return $this->jsonResponseService->successResponse($createResponse->response($createUser));
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param DetailResponse $detailResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/detail", methods={"GET"})
     */
    public function detail(Request $request, UserRepository $userRepository, DetailResponse $detailResponse)
    {
        try {
            $userId = $request->request->get('userId');
            if (is_null($userId)) {
                return $this->jsonResponseService->errorResponse('UserId Required', HttpStatusCodes::BAD_REQUEST);
            }
            $user = $userRepository->findById($userId);
            return $this->jsonResponseService->successResponse($detailResponse->response($user));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UpdateResponse $updateResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/update", methods={"POST"})
     */
    public function update(Request $request, UserRepository $userRepository, UpdateResponse $updateResponse)
    {
        try {
            $userId = $request->request->get('userId');
            if (is_null($userId)) {
                return $this->jsonResponseService->errorResponse('UserId Required', HttpStatusCodes::BAD_REQUEST);
            }
            $user = $userRepository->findById($userId);
            $updateUser = $userRepository->updateUser($user, $request);
            return $this->jsonResponseService->successResponse($updateResponse->response($updateUser));
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }
    }
}