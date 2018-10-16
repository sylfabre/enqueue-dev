<?php

namespace Enqueue\Dbal\Tests;

use Enqueue\Dbal\DbalDestination;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\DestinationInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;

class DbalDestinationTest extends \PHPUnit_Framework_TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementDestinationInterface()
    {
        $this->assertClassImplements(DestinationInterface::class, DbalDestination::class);
    }

    public function testShouldImplementTopicInterface()
    {
        $this->assertClassImplements(TopicInterface::class, DbalDestination::class);
    }

    public function testShouldImplementQueueInterface()
    {
        $this->assertClassImplements(QueueInterface::class, DbalDestination::class);
    }

    public function testShouldReturnTopicAndQueuePreviouslySetInConstructor()
    {
        $destination = new DbalDestination('topic-or-queue-name');

        $this->assertSame('topic-or-queue-name', $destination->getQueueName());
        $this->assertSame('topic-or-queue-name', $destination->getTopicName());
    }
}
