<?php

namespace Enqueue\Pheanstalk\Tests;

use Enqueue\Pheanstalk\PheanstalkDestination;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;
use PHPUnit\Framework\TestCase;

class PheanstalkDestinationTest extends TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementQueueInterface()
    {
        $this->assertClassImplements(QueueInterface::class, PheanstalkDestination::class);
    }

    public function testShouldImplementTopicInterface()
    {
        $this->assertClassImplements(TopicInterface::class, PheanstalkDestination::class);
    }

    public function testShouldAllowGetNameSetInConstructor()
    {
        $destination = new PheanstalkDestination('theDestinationName');

        $this->assertSame('theDestinationName', $destination->getName());
    }
}
