<?php

namespace Enqueue\Tests\Consumption\Extension;

use Enqueue\Consumption\Context\PostConsume;
use Enqueue\Consumption\Context\PostMessageReceived;
use Enqueue\Consumption\Context\PreConsume;
use Enqueue\Consumption\Extension\LimitConsumptionTimeExtension;
use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class LimitConsumptionTimeExtensionTest extends TestCase
{
    public function testCouldBeConstructedWithRequiredArguments()
    {
        new LimitConsumptionTimeExtension(new \DateTime('+1 day'));
    }

    public function testOnPreConsumeShouldInterruptExecutionIfConsumptionTimeExceeded()
    {
        $context = new PreConsume(
            $this->createInteropContextMock(),
            $this->createSubscriptionConsumerMock(),
            new NullLogger(),
            1,
            2,
            3
        );

        // guard
        $this->assertFalse($context->isExecutionInterrupted());

        // test
        $extension = new LimitConsumptionTimeExtension(new \DateTime('-2 second'));

        $extension->onPreConsume($context);

        $this->assertTrue($context->isExecutionInterrupted());
    }

    public function testOnPostConsumeShouldInterruptExecutionIfConsumptionTimeExceeded()
    {
        $postConsume = new PostConsume(
            $this->createInteropContextMock(),
            $this->createSubscriptionConsumerMock(),
            1,
            1,
            1,
            new NullLogger()
        );

        // guard
        $this->assertFalse($postConsume->isExecutionInterrupted());

        // test
        $extension = new LimitConsumptionTimeExtension(new \DateTime('-2 second'));

        $extension->onPostConsume($postConsume);

        $this->assertTrue($postConsume->isExecutionInterrupted());
    }

    public function testOnPostReceivedShouldInterruptExecutionIfConsumptionTimeExceeded()
    {
        $postReceivedMessage = new PostMessageReceived(
            $this->createInteropContextMock(),
            $this->createMock(ConsumerInterface::class),
            $this->createMock(MessageInterface::class),
            'aResult',
            1,
            new NullLogger()
        );

        // guard
        $this->assertFalse($postReceivedMessage->isExecutionInterrupted());

        // test
        $extension = new LimitConsumptionTimeExtension(new \DateTime('-2 second'));

        $extension->onPostMessageReceived($postReceivedMessage);

        $this->assertTrue($postReceivedMessage->isExecutionInterrupted());
    }

    public function testOnPreConsumeShouldNotInterruptExecutionIfConsumptionTimeIsNotExceeded()
    {
        $context = new PreConsume(
            $this->createInteropContextMock(),
            $this->createSubscriptionConsumerMock(),
            new NullLogger(),
            1,
            2,
            3
        );

        // guard
        $this->assertFalse($context->isExecutionInterrupted());

        // test
        $extension = new LimitConsumptionTimeExtension(new \DateTime('+2 second'));

        $extension->onPreConsume($context);

        $this->assertFalse($context->isExecutionInterrupted());
    }

    public function testOnPostConsumeShouldNotInterruptExecutionIfConsumptionTimeIsNotExceeded()
    {
        $postConsume = new PostConsume(
            $this->createInteropContextMock(),
            $this->createSubscriptionConsumerMock(),
            1,
            1,
            1,
            new NullLogger()
        );

        // guard
        $this->assertFalse($postConsume->isExecutionInterrupted());

        // test
        $extension = new LimitConsumptionTimeExtension(new \DateTime('+2 second'));

        $extension->onPostConsume($postConsume);

        $this->assertFalse($postConsume->isExecutionInterrupted());
    }

    public function testOnPostReceivedShouldNotInterruptExecutionIfConsumptionTimeIsNotExceeded()
    {
        $postReceivedMessage = new PostMessageReceived(
            $this->createInteropContextMock(),
            $this->createMock(ConsumerInterface::class),
            $this->createMock(MessageInterface::class),
            'aResult',
            1,
            new NullLogger()
        );

        // guard
        $this->assertFalse($postReceivedMessage->isExecutionInterrupted());

        // test
        $extension = new LimitConsumptionTimeExtension(new \DateTime('+2 second'));

        $extension->onPostMessageReceived($postReceivedMessage);

        $this->assertFalse($postReceivedMessage->isExecutionInterrupted());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createInteropContextMock(): ContextInterface
    {
        return $this->createMock(ContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createSubscriptionConsumerMock(): SubscriptionConsumerInterface
    {
        return $this->createMock(SubscriptionConsumerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createLoggerMock(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }
}
