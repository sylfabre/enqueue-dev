<?php

namespace Enqueue\Null;

use Interop\Queue\TopicInterface;

class NullTopic implements TopicInterface
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getTopicName(): string
    {
        return $this->name;
    }
}
