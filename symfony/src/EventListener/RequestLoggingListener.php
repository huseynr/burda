<?php

declare(strict_types=1);

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestLoggingListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ){
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $method = $request->getMethod();
        $uri = $request->getUri();
        $ip = $request->getClientIp();
        $userAgent = $request->headers->get('User-Agent');
        $logMessage = sprintf('Incoming request: %s %s from %s', $method, $uri, $ip);

        $this->logger->info($logMessage, ['user_agent' => $userAgent]);
    }
}
