<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 30.06.2020
 * Time: 15:13
 */


namespace App\Subscriber;


use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class RequestResponseLogSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface $loggerService
     */
    private $loggerService;

    /**
     * @var RequestStack $requestStack
     */
    private $requestStack;

    /**
     * @var string $uniqid
     */
    private $uniqid;

    public function __construct(LoggerInterface $loggerService, RequestStack $requestStack)
    {
        $this->loggerService = $loggerService;
        $this->uniqid = $requestStack->getCurrentRequest()->attributes->get('uniqid');
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 1],
            AuthenticationEvents::AUTHENTICATION_FAILURE => ['onAuthenticationFailure', 2],
            KernelEvents::RESPONSE => ['onKernelResponse', 3],
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        [$uri, $method, $headers, $parameters] = $this->extractDataFromRequest($request);

        $this->loggerService->log(Logger::INFO, '#REQ: ' . $this->uniqid . ' ' . $method . ' ' . $uri . ' #PARAMS: ' . json_encode($parameters) . ' #HEADERS: ' . json_encode($headers) . PHP_EOL);
    }

    public function onAuthenticationFailure(AuthenticationEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        [$uri, $method, $headers, $parameters] = $this->extractDataFromRequest($request);

        $this->loggerService->log(Logger::INFO, '#REQ: ' . $this->uniqid . ' ' . $method . ' ' . $uri . ' #PARAMS: ' . json_encode($parameters) . ' #HEADERS: ' . json_encode($headers) . PHP_EOL);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($event->getResponse()->headers->get('Content-Type') === 'application/json') {
            $response = json_decode($event->getResponse()->getContent(), true);

            if ($event->getResponse()->getStatusCode() < 500) {
                $this->loggerService->log(Logger::INFO, '#RES: ' . $this->uniqid . ' ' . json_encode($response) . PHP_EOL);
            } else {
                $this->loggerService->log(Logger::CRITICAL, '#RES: ' . $this->uniqid . ' ' . json_encode($response) . PHP_EOL);
            }
        }
    }

    private function extractDataFromRequest(Request $request): array
    {
        $uri = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . $request->getRequestUri();
        $method = $request->getRealMethod();
        $headers = $request->headers->all();

        if ('GET' === $method) {
            $parameters = $request->query->all();
        } else {
            $parameters = $request->request->all();
        }

        return [$uri, $method, $headers, $parameters];
    }
}