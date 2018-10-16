<?php

declare(strict_types=1);

namespace Enqueue\Gps;

use Interop\Queue\QueueInterface;

class GpsQueue implements QueueInterface
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getQueueName(): string
    {
        return $this->name;
    }
}
