<?php

namespace Enqueue\Pheanstalk\Tests;

use Enqueue\Null\NullQueue;
use Enqueue\Pheanstalk\PheanstalkContext;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\ContextInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\TemporaryQueueNotSupportedException;
use Pheanstalk\Connection;
use Pheanstalk\Pheanstalk;
use PHPUnit\Framework\TestCase;

class PheanstalkContextTest extends TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementContextInterface()
    {
        $this->assertClassImplements(ContextInterface::class, PheanstalkContext::class);
    }

    public function testCouldBeConstructedWithPheanstalkAsFirstArgument()
    {
        new PheanstalkContext($this->createPheanstalkMock());
    }

    public function testThrowNotImplementedOnCreateTemporaryQueue()
    {
        $context = new PheanstalkContext($this->createPheanstalkMock());

        $this->expectException(TemporaryQueueNotSupportedException::class);

        $context->createTemporaryQueue();
    }

    public function testThrowInvalidDestinationIfInvalidDestinationGivenOnCreateConsumer()
    {
        $context = new PheanstalkContext($this->createPheanstalkMock());

        $this->expectException(InvalidDestinationException::class);
        $context->createConsumer(new NullQueue('aQueue'));
    }

    public function testShouldAllowGetPheanstalkSetInConstructor()
    {
        $pheanstalk = $this->createPheanstalkMock();

        $context = new PheanstalkContext($pheanstalk);

        $this->assertSame($pheanstalk, $context->getPheanstalk());
    }

    public function testShouldDoConnectionDisconnectOnContextClose()
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('disconnect')
        ;

        $pheanstalk = $this->createPheanstalkMock();
        $pheanstalk
            ->expects($this->once())
            ->method('getConnection')
            ->willReturn($connection)
        ;

        $context = new PheanstalkContext($pheanstalk);

        $context->close();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Pheanstalk
     */
    private function createPheanstalkMock()
    {
        return $this->createMock(Pheanstalk::class);
    }
}
