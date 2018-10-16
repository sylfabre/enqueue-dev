<?php

namespace Enqueue\Gearman\Tests;

use Enqueue\Gearman\GearmanDestination;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;
use PHPUnit\Framework\TestCase;

class GearmanDestinationTest extends TestCase
{
    use ClassExtensionTrait;
    use SkipIfGearmanExtensionIsNotInstalledTrait;

    public function testShouldImplementQueueInterface()
    {
        $this->assertClassImplements(QueueInterface::class, GearmanDestination::class);
    }

    public function testShouldImplementTopicInterface()
    {
        $this->assertClassImplements(TopicInterface::class, GearmanDestination::class);
    }

    public function testShouldAllowGetNameSetInConstructor()
    {
        $destination = new GearmanDestination('theDestinationName');

        $this->assertSame('theDestinationName', $destination->getName());
    }
}
