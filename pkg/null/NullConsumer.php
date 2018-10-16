<?php

declare(strict_types=1);

namespace Enqueue\Null;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\DestinationInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\QueueInterface;

class NullConsumer implements ConsumerInterface
{
    /**
     * @var DestinationInterface
     */
    private $queue;

    public function __construct(DestinationInterface $queue)
    {
        $this->queue = $queue;
    }

    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }

    /**
     * @return NullMessage
     */
    public function receive(int $timeout = 0): ?MessageInterface
    {
        return null;
    }

    /**
     * @return NullMessage
     */
    public function receiveNoWait(): ?MessageInterface
    {
        return null;
    }

    public function acknowledge(MessageInterface $message): void
    {
    }

    public function reject(MessageInterface $message, bool $requeue = false): void
    {
    }
}
