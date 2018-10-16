<?php

namespace Enqueue\Dbal\Tests;

use Enqueue\Dbal\DbalContext;
use Enqueue\Dbal\DbalMessage;
use Enqueue\Dbal\DbalProducer;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\ProducerInterface;

class DbalProducerTest extends \PHPUnit_Framework_TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementProducerInterface()
    {
        $this->assertClassImplements(ProducerInterface::class, DbalProducer::class);
    }

    public function testCouldBeConstructedWithRequiredArguments()
    {
        new DbalProducer($this->createContextMock());
    }

    public function testShouldThrowIfDestinationOfInvalidType()
    {
        $this->expectException(InvalidDestinationException::class);
        $this->expectExceptionMessage(
            'The destination must be an instance of '.
            'Enqueue\Dbal\DbalDestination but got '.
            'Enqueue\Dbal\Tests\NotSupportedDestination1.'
        );

        $producer = new DbalProducer($this->createContextMock());

        $producer->send(new NotSupportedDestination1(), new DbalMessage());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DbalContext
     */
    private function createContextMock()
    {
        return $this->createMock(DbalContext::class);
    }
}

class NotSupportedDestination1 implements DestinationInterface
{
}
