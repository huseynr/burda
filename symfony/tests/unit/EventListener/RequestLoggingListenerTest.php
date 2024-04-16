<?php

declare(strict_types=1);

namespace App\Test\Unit\EventListener;

use App\EventListener\RequestLoggingListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(RequestLoggingListener::class)]
final class RequestLoggingListenerTest extends TestCase
{
    private $logger;
    private $listener;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->listener = new RequestLoggingListener($this->logger);
    }

    public function testOnKernelRequest(): void
    {
        $method = 'GET';
        $uri = 'http://localhost/';
        $ip = '127.0.0.1';
        $userAgent = 'Mozilla/5.0';
        $request = new Request([], [], [], [], [], ['REMOTE_ADDR' => $ip, 'HTTP_USER_AGENT' => $userAgent, 'REQUEST_URI' => $uri, 'REQUEST_METHOD' => $method]);
        $event = new RequestEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);

        $this->logger
            ->expects($this->once())
            ->method('info')
            ->with(
                $this->equalTo(sprintf('Incoming request: %s %s from %s', $method, 'http://:/', $ip)),
                $this->equalTo(['user_agent' => $userAgent])
            );

        $this->listener->onKernelRequest($event);

        $this->addToAssertionCount(1);
    }
}
