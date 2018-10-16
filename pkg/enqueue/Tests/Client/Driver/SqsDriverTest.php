<?php

namespace Enqueue\Tests\Client\Driver;

use Enqueue\Client\Driver\GenericDriver;
use Enqueue\Client\Driver\SqsDriver;
use Enqueue\Client\DriverInterface;
use Enqueue\Client\Route;
use Enqueue\Client\RouteCollection;
use Enqueue\Sqs\SqsContext;
use Enqueue\Sqs\SqsDestination;
use Enqueue\Sqs\SqsMessage;
use Enqueue\Sqs\SqsProducer;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\ProducerInterface as InteropProducer;
use Interop\Queue\QueueInterface as InteropQueue;
use Interop\Queue\TopicInterface as InteropTopic;
use PHPUnit\Framework\TestCase;

class SqsDriverTest extends TestCase
{
    use ClassExtensionTrait;
    use GenericDriverTestsTrait;

    public function testShouldImplementsDriverInterface()
    {
        $this->assertClassImplements(DriverInterface::class, SqsDriver::class);
    }

    public function testShouldBeSubClassOfGenericDriver()
    {
        $this->assertClassExtends(GenericDriver::class, SqsDriver::class);
    }

    public function testShouldSetupBroker()
    {
        $routerQueue = new SqsDestination('');
        $processorQueue = new SqsDestination('');

        $context = $this->createContextMock();
        // setup router
        $context
            ->expects($this->at(0))
            ->method('createQueue')
            ->with('aprefix_dot_default')
            ->willReturn($routerQueue)
        ;
        $context
            ->expects($this->at(1))
            ->method('declareQueue')
            ->with($this->identicalTo($routerQueue))
        ;
        // setup processor queue
        $context
            ->expects($this->at(2))
            ->method('createQueue')
            ->with('aprefix_dot_default')
            ->willReturn($processorQueue)
        ;
        $context
            ->expects($this->at(3))
            ->method('declareQueue')
            ->with($this->identicalTo($processorQueue))
        ;

        $driver = new SqsDriver(
            $context,
            $this->createDummyConfig(),
            new RouteCollection([
                new Route('topic', Route::TOPIC, 'processor'),
            ])
        );

        $driver->setupBroker();
    }

    protected function createDriver(...$args): DriverInterface
    {
        return new SqsDriver(...$args);
    }

    /**
     * @return SqsContext
     */
    protected function createContextMock(): ContextInterface
    {
        return $this->createMock(SqsContext::class);
    }

    /**
     * @return SqsProducer
     */
    protected function createProducerMock(): InteropProducer
    {
        return $this->createMock(SqsProducer::class);
    }

    /**
     * @return SqsDestination
     */
    protected function createQueue(string $name): InteropQueue
    {
        return new SqsDestination($name);
    }

    /**
     * @return SqsDestination
     */
    protected function createTopic(string $name): InteropTopic
    {
        return new SqsDestination($name);
    }

    /**
     * @return SqsMessage
     */
    protected function createMessage(): InteropMessage
    {
        return new SqsMessage();
    }

    protected function getPrefixAppFooQueueTransportName(): string
    {
        return 'aprefix_dot_anappname_dot_afooqueue';
    }

    protected function getPrefixFooQueueTransportName(): string
    {
        return 'aprefix_dot_afooqueue';
    }

    protected function getAppFooQueueTransportName(): string
    {
        return 'anappname_dot_afooqueue';
    }

    protected function getDefaultQueueTransportName(): string
    {
        return 'aprefix_dot_default';
    }

    protected function getCustomQueueTransportName(): string
    {
        return 'aprefix_dot_custom';
    }

    protected function getRouterTransportName(): string
    {
        return 'aprefix_dot_default';
    }
}
