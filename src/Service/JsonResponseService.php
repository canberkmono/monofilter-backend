<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 30.06.2020
 * Time: 15:12
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

final class JsonResponseService
{
    /** @var RequestStack $requestStack */
    private $requestStack;

    /**
     * JsonResponseService constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function successResponse($data = [], int $status = 200, array $headers = []): JsonResponse
    {
        $response = [
            'meta' => [
                'success' => true,
                'statusCode' => $status,
                'requestId' => $this->requestStack->getCurrentRequest()->attributes->get('uniqid'),
            ],
            'data' => $data
        ];

        $response = new JsonResponse($response, $status, $headers);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param string $message
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function errorResponse(string $message = 'Whoops, looks like something went wrong.', int $status = 418, array $headers = []): JsonResponse
    {
        $response = [
            'meta' => [
                'success' => false,
                'statusCode' => $status,
                'requestId' => $this->requestStack->getCurrentRequest()->attributes->get('uniqid'),
            ],
            'data' => [
                'message' => $message,
            ],
        ];

        $response = new JsonResponse($response, $status, $headers);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}