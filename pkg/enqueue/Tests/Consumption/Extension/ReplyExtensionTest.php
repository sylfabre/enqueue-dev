<?php

namespace Enqueue\Tests\Consumption\Extension;

use Enqueue\Consumption\Context\PostMessageReceived;
use Enqueue\Consumption\Extension\ReplyExtension;
use Enqueue\Consumption\PostMessageReceivedExtensionInterface;
use Enqueue\Consumption\Result;
use Enqueue\Null\NullMessage;
use Enqueue\Null\NullQueue;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\ProducerInterface as InteropProducer;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ReplyExtensionTest extends TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementPostMessageReceivedExtensionInterface()
    {
        $this->assertClassImplements(PostMessageReceivedExtensionInterface::class, ReplyExtension::class);
    }

    public function testCouldBeConstructedWithoutAnyArguments()
    {
        new ReplyExtension();
    }

    public function testShouldDoNothingIfReceivedMessageNotHaveReplyToSet()
    {
        $extension = new ReplyExtension();

        $postReceivedMessage = new PostMessageReceived(
            $this->createNeverUsedContextMock(),
            $this->createMock(ConsumerInterface::class),
            new NullMessage(),
            'aResult',
            1,
            new NullLogger()
        );

        $extension->onPostMessageReceived($postReceivedMessage);
    }

    public function testShouldDoNothingIfContextResultIsNotInstanceOfResult()
    {
        $extension = new ReplyExtension();

        $message = new NullMessage();
        $message->setReplyTo('aReplyToQueue');

        $postReceivedMessage = new PostMessageReceived(
            $this->createNeverUsedContextMock(),
            $this->createMock(ConsumerInterface::class),
            $message,
            'notInstanceOfResult',
            1,
            new NullLogger()
        );

        $extension->onPostMessageReceived($postReceivedMessage);
    }

    public function testShouldDoNothingIfResultInstanceOfResultButReplyMessageNotSet()
    {
        $extension = new ReplyExtension();

        $message = new NullMessage();
        $message->setReplyTo('aReplyToQueue');

        $postReceivedMessage = new PostMessageReceived(
            $this->createNeverUsedContextMock(),
            $this->createMock(ConsumerInterface::class),
            $message,
            Result::ack(),
            1,
            new NullLogger()
        );

        $extension->onPostMessageReceived($postReceivedMessage);
    }

    public function testShouldSendReplyMessageToReplyQueueOnPostReceived()
    {
        $extension = new ReplyExtension();

        $message = new NullMessage();
        $message->setReplyTo('aReplyToQueue');
        $message->setCorrelationId('theCorrelationId');

        $replyMessage = new NullMessage();
        $replyMessage->setCorrelationId('theCorrelationId');

        $replyQueue = new NullQueue('aReplyName');

        $producerMock = $this->createMock(InteropProducer::class);
        $producerMock
            ->expects($this->once())
            ->method('send')
            ->with($replyQueue, $replyMessage)
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|ContextInterface $contextMock */
        $contextMock = $this->createMock(ContextInterface::class);
        $contextMock
            ->expects($this->once())
            ->method('createQueue')
            ->willReturn($replyQueue)
        ;
        $contextMock
            ->expects($this->once())
            ->method('createProducer')
            ->willReturn($producerMock)
        ;

        $postReceivedMessage = new PostMessageReceived(
            $contextMock,
            $this->createMock(ConsumerInterface::class),
            $message,
            Result::reply($replyMessage),
            1,
            new NullLogger()
        );

        $extension->onPostMessageReceived($postReceivedMessage);
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
    private function createNeverUsedContextMock(): ContextInterface
    {
        $contextMock = $this->createMock(ContextInterface::class);
        $contextMock
            ->expects($this->never())
            ->method('createProducer')
        ;

        return $contextMock;
    }
}
