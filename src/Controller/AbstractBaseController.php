<?php

namespace App\Controller;

use App\Service\JsonResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AbstractBaseController
 * @package App\Controller
 *
 */
abstract class AbstractBaseController extends AbstractController
{
    /**
     * @var JsonResponseService $jsonResponseService
     */
    protected $jsonResponseService;

    /**
     * @var LoggerInterface $loggerService
     */
    protected $loggerService;

    public function __construct(JsonResponseService $jsonResponseService, LoggerInterface $loggerService)
    {
        $this->jsonResponseService = $jsonResponseService;
        $this->loggerService = $loggerService;
    }
}