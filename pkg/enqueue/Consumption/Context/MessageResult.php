<?php

namespace Enqueue\Consumption\Context;

use Enqueue\Consumption\Result;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Psr\Log\LoggerInterface;

final class MessageResult
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

    public function __construct(ContextInterface $context, MessageInterface $message, $result, int $receivedAt, LoggerInterface $logger)
    {
        $this->context = $context;
        $this->message = $message;
        $this->logger = $logger;
        $this->result = $result;
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

    /**
     * @param Result|string|object|null $result
     */
    public function changeResult($result): void
    {
        $this->result = $result;
    }
}
