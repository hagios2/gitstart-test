<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (
            $exception instanceof NotFoundHttpException
        ) {
            $response = new JsonResponse([
                'message' => 'Route/Entity not found',
                'status' => 404
            ], 404);

            $event->setResponse($response);
        }
    }
}
