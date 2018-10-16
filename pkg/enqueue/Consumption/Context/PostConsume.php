<?php

namespace Enqueue\Consumption\Context;

use Interop\Queue\ContextInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use Psr\Log\LoggerInterface;

final class PostConsume
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var SubscriptionConsumerInterface
     */
    private $subscriptionConsumer;

    /**
     * @var int
     */
    private $receivedMessagesCount;

    /**
     * @var int
     */
    private $cycle;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $executionInterrupted;

    public function __construct(ContextInterface $context, SubscriptionConsumerInterface $subscriptionConsumer, int $receivedMessagesCount, int $cycle, int $startTime, LoggerInterface $logger)
    {
        $this->context = $context;
        $this->subscriptionConsumer = $subscriptionConsumer;
        $this->receivedMessagesCount = $receivedMessagesCount;
        $this->cycle = $cycle;
        $this->startTime = $startTime;
        $this->logger = $logger;

        $this->executionInterrupted = false;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function getSubscriptionConsumer(): SubscriptionConsumerInterface
    {
        return $this->subscriptionConsumer;
    }

    public function getReceivedMessagesCount(): int
    {
        return $this->receivedMessagesCount;
    }

    public function getCycle(): int
    {
        return $this->cycle;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
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
