<?php

namespace Enqueue\Mongodb\Tests;

use Enqueue\Mongodb\MongodbContext;
use Enqueue\Mongodb\MongodbMessage;
use Enqueue\Mongodb\MongodbProducer;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\ProducerInterface;

/**
 * @group mongodb
 */
class MongodbProducerTest extends \PHPUnit_Framework_TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementProducerInterface()
    {
        $this->assertClassImplements(ProducerInterface::class, MongodbProducer::class);
    }

    public function testCouldBeConstructedWithRequiredArguments()
    {
        new MongodbProducer($this->createContextMock());
    }

    public function testShouldThrowIfDestinationOfInvalidType()
    {
        $this->expectException(InvalidDestinationException::class);
        $this->expectExceptionMessage(
            'The destination must be an instance of '.
            'Enqueue\Mongodb\MongodbDestination but got '.
            'Enqueue\Mongodb\Tests\NotSupportedDestination1.'
        );

        $producer = new MongodbProducer($this->createContextMock());

        $producer->send(new NotSupportedDestination1(), new MongodbMessage());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|MongodbContext
     */
    private function createContextMock()
    {
        return $this->createMock(MongodbContext::class);
    }
}

class NotSupportedDestination1 implements DestinationInterface
{
}
