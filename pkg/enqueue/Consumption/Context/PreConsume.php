<?php

namespace Enqueue\Consumption\Context;

use Interop\Queue\ContextInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use Psr\Log\LoggerInterface;

final class PreConsume
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $cycle;

    /**
     * @var int
     */
    private $receiveTimeout;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var bool
     */
    private $executionInterrupted;

    public function __construct(ContextInterface $context, SubscriptionConsumerInterface $subscriptionConsumer, LoggerInterface $logger, int $cycle, int $receiveTimeout, int $startTime)
    {
        $this->context = $context;
        $this->subscriptionConsumer = $subscriptionConsumer;
        $this->logger = $logger;
        $this->cycle = $cycle;
        $this->receiveTimeout = $receiveTimeout;
        $this->startTime = $startTime;

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

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getCycle(): int
    {
        return $this->cycle;
    }

    public function getReceiveTimeout(): int
    {
        return $this->receiveTimeout;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
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
