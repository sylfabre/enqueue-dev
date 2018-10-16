<?php

namespace Enqueue\Consumption\Context;

use Enqueue\Consumption\Result;
use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Psr\Log\LoggerInterface;

final class PostMessageReceived
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ConsumerInterface
     */
    private $consumer;

    /**
     * @var MessageInterface
     */
    private $message;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $receivedAt;

    /**
     * @var Result|string|object|null
     */
    private $result;

    /**
     * @var bool
     */
    private $executionInterrupted;

    public function __construct(
        ContextInterface $context,
        ConsumerInterface $consumer,
        MessageInterface $message,
        $result,
        int $receivedAt,
        LoggerInterface $logger
    ) {
        $this->context = $context;
        $this->consumer = $consumer;
        $this->message = $message;
        $this->result = $result;
        $this->receivedAt = $receivedAt;
        $this->logger = $logger;

        $this->executionInterrupted = false;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function getConsumer(): ConsumerInterface
    {
        return $this->consumer;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getReceivedAt(): int
    {
        return $this->receivedAt;
    }

    /**
     * @return Result|null|object|string
     */
    public function getResult()
    {
        return $this->result;
    }

    public function isExecutionInterrupted(): bool
    {
        return $this->executionInterrupted;
    }

    public function interruptExecution(): void
    {
        $this->executionInterrupted = true;
    }
}
