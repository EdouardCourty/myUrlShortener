<?php

namespace App\EventSubscriber;

use App\Exception\Repository\LinkNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private const APP_ENV_KEY = 'APP_ENV';

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['handleKernelException']
        ];
    }

    public function handleKernelException(ExceptionEvent $event): ExceptionEvent
    {
        $env = array_key_exists(self::APP_ENV_KEY, $_ENV) ? $_ENV[self::APP_ENV_KEY] : $_SERVER[self::APP_ENV_KEY];

        $exception = $event->getThrowable();

        switch ($env) {
            case 'dev':
                return $event;

            case 'staging':
                $responseData = [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString()
                ];
                break;

            default:
                $responseData = [
                    'message' => $exception->getMessage()
                ];
                break;
        }

        $status = match(get_class($exception)) {
            NotFoundHttpException::class, LinkNotFoundException::class => Response::HTTP_NOT_FOUND,
            InvalidArgumentException::class => Response::HTTP_BAD_REQUEST,
            default => Response::HTTP_INTERNAL_SERVER_ERROR
        };

        $event->setResponse(new JsonResponse($responseData, $status));

        return $event;
    }
}
