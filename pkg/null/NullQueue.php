<?php

declare(strict_types=1);

namespace Enqueue\Null;

use Interop\Queue\QueueInterface;

class NullQueue implements QueueInterface
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
