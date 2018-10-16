<?php

namespace Enqueue\Consumption;

use Interop\Queue\ProcessorInterface;
use Interop\Queue\QueueInterface;

final class BoundProcessor
{
    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    public function __construct(QueueInterface $queue, ProcessorInterface $processor)
    {
        $this->queue = $queue;
        $this->processor = $processor;
    }

    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }

    public function getProcessor(): ProcessorInterface
    {
        return $this->processor;
    }
}
