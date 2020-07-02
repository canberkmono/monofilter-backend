<?php

namespace App\Controller\Admin;

use App\Enums\HttpStatusCodes;
use App\Exceptions\Login\EmailNotFoundException;
use App\Exceptions\User\UserTokenUpdateException;
use App\Helpers\TokenHelper;
use App\Repository\UserRepository;
use App\Responses\Login\LoginResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class LoginController
 * @package App\Controller\Admin
 *
 * @Route("/")
 */
class LoginController extends AbstractBaseController
{
    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param LoginResponse $loginResponse
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, LoginResponse $loginResponse, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        try {
            $user = $userRepository->findByEmail($email);
            if (!$user || !$userPasswordEncoder->isPasswordValid($user, $password)) {
                return $this->jsonResponseService->errorResponse("Hatalı Giriş Bilgileri", HttpStatusCodes::INVALID_LOGIN_CREDENTIAL);
            }
        } catch (EmailNotFoundException $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        $token = TokenHelper::generateToken();

        try {
            $userRepository->updateUserToken($user, $token);
        } catch (UserTokenUpdateException $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

        return $this->jsonResponseService->successResponse($loginResponse->response($user));
    }
}