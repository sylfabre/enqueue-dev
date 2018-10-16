<?php

declare(strict_types=1);

namespace Enqueue\Gearman;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\QueueInterface;

class GearmanConsumer implements ConsumerInterface
{
    /**
     * @var \GearmanWorker
     */
    private $worker;

    /**
     * @var GearmanDestination
     */
    private $destination;

    /**
     * @var GearmanContext
     */
    private $context;

    public function __construct(GearmanContext $context, GearmanDestination $destination)
    {
        $this->context = $context;
        $this->destination = $destination;

        $this->worker = $context->createWorker();
    }

    /**
     * @return GearmanDestination
     */
    public function getQueue(): QueueInterface
    {
        return $this->destination;
    }

    /**
     * @return GearmanMessage
     */
    public function receive(int $timeout = 0): ?MessageInterface
    {
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        $this->worker->setTimeout($timeout);

        try {
            $message = null;

            $this->worker->addFunction($this->destination->getName(), function (\GearmanJob $job) use (&$message) {
                $message = GearmanMessage::jsonUnserialize($job->workload());
            });

            while ($this->worker->work());
        } finally {
            restore_error_handler();
        }

        return $message;
    }

    /**
     * @return GearmanMessage
     */
    public function receiveNoWait(): ?MessageInterface
    {
        return $this->receive(100);
    }

    /**
     * @param GearmanMessage $message
     */
    public function acknowledge(MessageInterface $message): void
    {
    }

    /**
     * @param GearmanMessage $message
     */
    public function reject(MessageInterface $message, bool $requeue = false): void
    {
        if ($requeue) {
            $this->context->createProducer()->send($this->destination, $message);
        }
    }

    public function getWorker(): \GearmanWorker
    {
        return $this->worker;
    }
}
