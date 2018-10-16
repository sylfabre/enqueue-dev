<?php

namespace Enqueue\Tests\Client\Driver;

use Enqueue\Client\Config;
use Enqueue\Client\Driver\GenericDriver;
use Enqueue\Client\DriverInterface;
use Enqueue\Client\MessagePriority;
use Enqueue\Null\NullMessage;
use Enqueue\Null\NullQueue;
use Enqueue\Null\NullTopic;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\ProducerInterface as InteropProducer;
use Interop\Queue\QueueInterface as InteropQueue;
use Interop\Queue\TopicInterface as InteropTopic;
use PHPUnit\Framework\TestCase;

class GenericDriverTest extends TestCase
{
    use ClassExtensionTrait;
    use GenericDriverTestsTrait;

    public function testShouldImplementsDriverInterface()
    {
        $this->assertClassImplements(DriverInterface::class, GenericDriver::class);
    }

    protected function createDriver(...$args): DriverInterface
    {
        return new GenericDriver(...$args);
    }

    protected function createContextMock(): ContextInterface
    {
        return $this->createMock(ContextInterface::class);
    }

    protected function createProducerMock(): InteropProducer
    {
        return $this->createMock(InteropProducer::class);
    }

    protected function createQueue(string $name): InteropQueue
    {
        return new NullQueue($name);
    }

    protected function createTopic(string $name): InteropTopic
    {
        return new NullTopic($name);
    }

    protected function createMessage(): InteropMessage
    {
        return new NullMessage();
    }

    protected function assertTransportMessage(InteropMessage $transportMessage): void
    {
        $this->assertSame('body', $transportMessage->getBody());
        $this->assertArraySubset([
            'hkey' => 'hval',
            'message_id' => 'theMessageId',
            'timestamp' => 1000,
            'reply_to' => 'theReplyTo',
            'correlation_id' => 'theCorrelationId',
        ], $transportMessage->getHeaders());
        $this->assertEquals([
            'pkey' => 'pval',
            Config::CONTENT_TYPE => 'ContentType',
            Config::PRIORITY => MessagePriority::HIGH,
            Config::EXPIRE => 123,
            Config::DELAY => 345,
        ], $transportMessage->getProperties());
        $this->assertSame('theMessageId', $transportMessage->getMessageId());
        $this->assertSame(1000, $transportMessage->getTimestamp());
        $this->assertSame('theReplyTo', $transportMessage->getReplyTo());
        $this->assertSame('theCorrelationId', $transportMessage->getCorrelationId());
    }
}
