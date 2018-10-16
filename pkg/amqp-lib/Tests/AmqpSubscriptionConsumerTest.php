<?php

namespace Enqueue\AmqpLib\Tests;

use Enqueue\AmqpLib\AmqpContext;
use Enqueue\AmqpLib\AmqpSubscriptionConsumer;
use Interop\Queue\SubscriptionConsumerInterface;
use PHPUnit\Framework\TestCase;

class AmqpSubscriptionConsumerTest extends TestCase
{
    public function testShouldImplementSubscriptionConsumerInterface()
    {
        $rc = new \ReflectionClass(AmqpSubscriptionConsumer::class);

        $this->assertTrue($rc->implementsInterface(SubscriptionConsumerInterface::class));
    }

    public function testCouldBeConstructedWithAmqpContextAsFirstArgument()
    {
        new AmqpSubscriptionConsumer($this->createAmqpContextMock());
    }

    /**
     * @return AmqpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createAmqpContextMock()
    {
        return $this->createMock(AmqpContext::class);
    }
}
