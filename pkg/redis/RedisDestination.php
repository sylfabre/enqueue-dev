<?php

declare(strict_types=1);

namespace Enqueue\Redis;

use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;

class RedisDestination implements QueueInterface, TopicInterface
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQueueName(): string
    {
        return $this->getName();
    }

    public function getTopicName(): string
    {
        return $this->getName();
    }
}
