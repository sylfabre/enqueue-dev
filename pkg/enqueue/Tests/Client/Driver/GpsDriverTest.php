<?php

namespace Enqueue\Tests\Client\Driver;

use Enqueue\Client\Driver\GenericDriver;
use Enqueue\Client\Driver\GpsDriver;
use Enqueue\Client\DriverInterface;
use Enqueue\Client\Route;
use Enqueue\Client\RouteCollection;
use Enqueue\Gps\GpsContext;
use Enqueue\Gps\GpsMessage;
use Enqueue\Gps\GpsProducer;
use Enqueue\Gps\GpsQueue;
use Enqueue\Gps\GpsTopic;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\ProducerInterface as InteropProducer;
use Interop\Queue\QueueInterface as InteropQueue;
use Interop\Queue\TopicInterface as InteropTopic;
use PHPUnit\Framework\TestCase;

class GpsDriverTest extends TestCase
{
    use ClassExtensionTrait;
    use GenericDriverTestsTrait;

    public function testShouldImplementsDriverInterface()
    {
        $this->assertClassImplements(DriverInterface::class, GpsDriver::class);
    }

    public function testShouldBeSubClassOfGenericDriver()
    {
        $this->assertClassExtends(GenericDriver::class, GpsDriver::class);
    }

    public function testShouldSetupBroker()
    {
        $routerTopic = new GpsTopic('');
        $routerQueue = new GpsQueue('');

        $processorTopic = new GpsTopic($this->getDefaultQueueTransportName());
        $processorQueue = new GpsQueue($this->getDefaultQueueTransportName());

        $context = $this->createContextMock();
        // setup router
        $context
            ->expects($this->at(0))
            ->method('createTopic')
            ->willReturn($routerTopic)
        ;
        $context
            ->expects($this->at(1))
            ->method('createQueue')
            ->willReturn($routerQueue)
        ;
        $context
            ->expects($this->at(2))
            ->method('subscribe')
            ->with($this->identicalTo($routerTopic), $this->identicalTo($routerQueue))
        ;
        $context
            ->expects($this->at(3))
            ->method('createQueue')
            ->with($this->getDefaultQueueTransportName())
            ->willReturn($processorQueue)
        ;
        // setup processor queue
        $context
            ->expects($this->at(4))
            ->method('createTopic')
            ->with($this->getDefaultQueueTransportName())
            ->willReturn($processorTopic)
        ;
        $context
            ->expects($this->at(5))
            ->method('subscribe')
            ->with($this->identicalTo($processorTopic), $this->identicalTo($processorQueue))
        ;

        $driver = new GpsDriver(
            $context,
            $this->createDummyConfig(),
            new RouteCollection([
                new Route('aTopic', Route::TOPIC, 'aProcessor'),
            ])
        );

        $driver->setupBroker();
    }

    protected function createDriver(...$args): DriverInterface
    {
        return new GpsDriver(...$args);
    }

    /**
     * @return GpsContext
     */
    protected function createContextMock(): ContextInterface
    {
        return $this->createMock(GpsContext::class);
    }

    /**
     * @return GpsProducer
     */
    protected function createProducerMock(): InteropProducer
    {
        return $this->createMock(GpsProducer::class);
    }

    /**
     * @return GpsQueue
     */
    protected function createQueue(string $name): InteropQueue
    {
        return new GpsQueue($name);
    }

    /**
     * @return GpsTopic
     */
    protected function createTopic(string $name): InteropTopic
    {
        return new GpsTopic($name);
    }

    /**
     * @return GpsMessage
     */
    protected function createMessage(): InteropMessage
    {
        return new GpsMessage();
    }

    protected function getRouterTransportName(): string
    {
        return 'aprefix.router';
    }
}
