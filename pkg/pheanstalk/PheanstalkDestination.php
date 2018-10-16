<?php

declare(strict_types=1);

namespace Enqueue\Pheanstalk;

use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;

class PheanstalkDestination implements QueueInterface, TopicInterface
{
    /**
     * @var string
     */
    private $destinationName;

    public function __construct(string $destinationName)
    {
        $this->destinationName = $destinationName;
    }

    public function getName(): string
    {
        return $this->destinationName;
    }

    public function getQueueName(): string
    {
        return $this->destinationName;
    }

    public function getTopicName(): string
    {
        return $this->destinationName;
    }
}
