<?php

declare(strict_types=1);

namespace Enqueue\Mongodb;

use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;

class MongodbDestination implements TopicInterface, QueueInterface
{
    /**
     * @var string
     */
    private $destinationName;

    public function __construct(string $name)
    {
        $this->destinationName = $name;
    }

    public function getQueueName(): string
    {
        return $this->destinationName;
    }

    public function getTopicName(): string
    {
        return $this->destinationName;
    }

    public function getName(): string
    {
        return $this->destinationName;
    }
}
