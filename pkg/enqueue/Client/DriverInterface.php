<?php

declare(strict_types=1);

namespace Enqueue\Client;

use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\QueueInterface as InteropQueue;
use Psr\Log\LoggerInterface;

interface DriverInterface
{
    public function createTransportMessage(Message $message): InteropMessage;

    public function createClientMessage(InteropMessage $message): Message;

    public function sendToRouter(Message $message): void;

    public function sendToProcessor(Message $message): void;

    public function createQueue(string $queueName, bool $prefix = true): InteropQueue;

    public function createRouteQueue(Route $route): InteropQueue;

    /**
     * Prepare broker for work.
     * Creates all required queues, exchanges, topics, bindings etc.
     */
    public function setupBroker(LoggerInterface $logger = null): void;

    public function getConfig(): Config;

    public function getContext(): ContextInterface;

    public function getRouteCollection(): RouteCollection;
}
