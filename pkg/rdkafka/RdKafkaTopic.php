<?php

declare(strict_types=1);

namespace Enqueue\RdKafka;

use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;
use RdKafka\TopicConf;

class RdKafkaTopic implements TopicInterface, QueueInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var TopicConf
     */
    private $conf;

    /**
     * @var int
     */
    private $partition;

    /**
     * @var string|null
     */
    private $key;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getTopicName(): string
    {
        return $this->name;
    }

    public function getQueueName(): string
    {
        return $this->name;
    }

    public function getConf(): ?TopicConf
    {
        return $this->conf;
    }

    public function setConf(TopicConf $conf = null): void
    {
        $this->conf = $conf;
    }

    public function getPartition(): ?int
    {
        return $this->partition;
    }

    public function setPartition(int $partition = null): void
    {
        $this->partition = $partition;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key = null): void
    {
        $this->key = $key;
    }
}
