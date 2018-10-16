<?php

namespace Enqueue\Consumption\Context;

use Enqueue\Consumption\Result;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Psr\Log\LoggerInterface;

final class ProcessorException
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var MessageInterface
     */
    private $message;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var Result|string|object|null
     */
    private $result;

    /**
     * @var int
     */
    private $receivedAt;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContextInterface $context, MessageInterface $message, \Exception $exception, int $receivedAt, LoggerInterface $logger)
    {
        $this->context = $context;
        $this->message = $message;
        $this->exception = $exception;
        $this->logger = $logger;
        $this->receivedAt = $receivedAt;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    public function getException(): \Exception
    {
        return $this->exception;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getReceivedAt(): int
    {
        return $this->receivedAt;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public function setResult(Result $result): void
    {
        $this->result = $result;
    }
}
