<?php

namespace Enqueue\Consumption\Context;

use Enqueue\Consumption\Result;
use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProcessorInterface;
use Psr\Log\LoggerInterface;

final class MessageReceived
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
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $receivedAt;

    /**
     * @var Result|null
     */
    private $result;

    public function __construct(
        ContextInterface $context,
        ConsumerInterface $consumer,
        MessageInterface $message,
        ProcessorInterface $processor,
        int $receivedAt,
        LoggerInterface $logger
    ) {
        $this->context = $context;
        $this->consumer = $consumer;
        $this->message = $message;
        $this->processor = $processor;
        $this->receivedAt = $receivedAt;
        $this->logger = $logger;
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

    public function getProcessor(): ProcessorInterface
    {
        return $this->processor;
    }

    public function changeProcessor(ProcessorInterface $processor): void
    {
        $this->processor = $processor;
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
