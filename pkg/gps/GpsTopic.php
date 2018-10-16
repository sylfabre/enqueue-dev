<?php

declare(strict_types=1);

namespace Enqueue\Gps;

use Interop\Queue\TopicInterface;

class GpsTopic implements TopicInterface
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
