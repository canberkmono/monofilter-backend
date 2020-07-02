<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 30.06.2020
 * Time: 15:13
 */

namespace App\Subscriber;


use App\Service\JsonResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $jsonResponseService;
    private $uniqid;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack, JsonResponseService $jsonResponseService)
    {
        $this->logger = $logger;
        $this->uniqid = $requestStack->getCurrentRequest()->attributes->get('uniqid');
        $this->jsonResponseService = $jsonResponseService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 1],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . $request->getRequestUri();
        $method = $request->getRealMethod();
        $headers = $request->headers->all();

        if ('GET' === $method) {
            $parameters = $request->query->all();
        } else {
            $parameters = $request->request->all();
        }

        $this->logger->error('#REQ: ' . $this->uniqid . ' ' . $method . ' ' . $uri . ' #PARAMS: ' . json_encode($parameters) . ' #HEADERS: ' . json_encode($headers) . PHP_EOL);

        $this->logger->error($event->getThrowable()->getMessage() . ' #FILE: ' . $event->getThrowable()->getFile() . ' #LINE: ' . $event->getThrowable()->getLine());

        return $this->jsonResponseService->errorResponse('Whoops, looks like something went wrong.', Response::HTTP_INTERNAL_SERVER_ERROR)->send();
    }
}