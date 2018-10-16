<?php

namespace Enqueue\Redis\Tests;

use Enqueue\Redis\RedisDestination;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;

class RedisDestinationTest extends \PHPUnit\Framework\TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementsTopicAndQueueInterfaces()
    {
        $this->assertClassImplements(TopicInterface::class, RedisDestination::class);
        $this->assertClassImplements(QueueInterface::class, RedisDestination::class);
    }

    public function testShouldReturnNameSetInConstructor()
    {
        $destination = new RedisDestination('aDestinationName');

        $this->assertSame('aDestinationName', $destination->getName());
        $this->assertSame('aDestinationName', $destination->getQueueName());
        $this->assertSame('aDestinationName', $destination->getTopicName());
    }
}
