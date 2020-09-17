<?php

namespace Calendar\Domain\Core\EventSubscriber;

use Calendar\Domain\Booking\Exception\BookingException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * for debugging you may use:
 * php bin/console debug:event-dispatcher
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    private const INTERNAL_ERROR = 'internal_server_error';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;


    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * return the subscribed events, their methods and priorities
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['logException', 20],
                ['returnFormattedResponse', -10],
            ],
        ];
    }


    /**
     * @param ExceptionEvent $event
     *
     * @throws \Exception
     */
    public function returnFormattedResponse(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = $this->getResponseWithException($exception);

        $event->setResponse($response);
    }


    /**
     * @param ExceptionEvent $event
     */
    public function logException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        switch (true) {
            case $exception instanceof BookingException:
                return;
            default:
                $this->logger->error(
                    $exception->getMessage(),
                    [
                        'class' => get_class($exception),
                        'trace' => $exception->getMessage(),
                    ]
                );
        }
    }

    /**
     * @param \Throwable $e
     *
     * @return JsonResponse
     */
    public function getResponseWithException(\Throwable $e): JsonResponse
    {
        switch (true) {
            case $e instanceof BookingException:
                $code = $e->getMessage();
                $status = Response::HTTP_BAD_REQUEST;
                break;
            default:
                $code = $e->getMessage();
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        $data = [
            'code' => $code,
            'trace' => $e->getTraceAsString(),
            'status' => $status,
        ];

        return new JsonResponse($data, $status);
    }
}
