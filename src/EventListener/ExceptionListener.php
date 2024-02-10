<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $statusCode = ($exception instanceof HttpExceptionInterface) ? $exception->getStatusCode() : 500;

        if ($exception instanceof NotFoundHttpException) {
            $response = new JsonResponse([
                'error' => 'Resource not found',
            ], 404);
        } elseif ($exception instanceof UnauthorizedHttpException) {
            $response = new JsonResponse([
                'error' => 'Unauthorized',
            ], 401);
        } else {
            $response = new JsonResponse([
                'error' => $exception->getMessage(),
            ], $statusCode);
        }

        $event->setResponse($response);
    }
}
