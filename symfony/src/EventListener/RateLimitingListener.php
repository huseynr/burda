<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

final class RateLimitingListener
{
    public function __construct(
        private readonly RateLimiterFactory $anonymousApiLimiter
    ){
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $limiter = $this->anonymousApiLimiter
            ->create($event->getRequest()->getClientIp());

        if (false === $limiter->consume(1)->isAccepted()) {
            $event->setResponse(
                new Response('Too many requests', Response::HTTP_TOO_MANY_REQUESTS)
            );
        }
    }
}
