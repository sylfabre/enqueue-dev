<?php

namespace Enqueue\Consumption\Context;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\ProcessorInterface;
use Psr\Log\LoggerInterface;

final class PreSubscribe
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @var ConsumerInterface
     */
    private $consumer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContextInterface $context, ProcessorInterface $processor, ConsumerInterface $consumer, LoggerInterface $logger)
    {
        $this->context = $context;
        $this->processor = $processor;
        $this->consumer = $consumer;
        $this->logger = $logger;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function getProcessor(): ProcessorInterface
    {
        return $this->processor;
    }

    public function getConsumer(): ConsumerInterface
    {
        return $this->consumer;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
