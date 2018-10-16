<?php

namespace Enqueue\Mongodb\Tests;

use Enqueue\Mongodb\MongodbDestination;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\DestinationInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;

/**
 * @group mongodb
 */
class MongodbDestinationTest extends \PHPUnit_Framework_TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementDestinationInterface()
    {
        $this->assertClassImplements(DestinationInterface::class, MongodbDestination::class);
    }

    public function testShouldImplementTopicInterface()
    {
        $this->assertClassImplements(TopicInterface::class, MongodbDestination::class);
    }

    public function testShouldImplementQueueInterface()
    {
        $this->assertClassImplements(QueueInterface::class, MongodbDestination::class);
    }

    public function testShouldReturnTopicAndQueuePreviouslySetInConstructor()
    {
        $destination = new MongodbDestination('topic-or-queue-name');

        $this->assertSame('topic-or-queue-name', $destination->getName());
        $this->assertSame('topic-or-queue-name', $destination->getTopicName());
    }
}
