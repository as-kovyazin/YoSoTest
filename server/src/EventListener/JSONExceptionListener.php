<?php
namespace App\EventListener;



use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class JSONExceptionListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 200],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $content = [
            'code' => $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500,
            'message' => $exception->getMessage(),
        ];

        if (in_array($_ENV['APP_ENV'], ['dev', 'test'], true)) {
            $content['trace'] = $exception->getTrace();
        }

        $event->setResponse(
            new JsonResponse($content, $content['code'])
        );
    }
}