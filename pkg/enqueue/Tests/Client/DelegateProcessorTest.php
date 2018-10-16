<?php

namespace Enqueue\Tests\Client;

use Enqueue\Client\Config;
use Enqueue\Client\DelegateProcessor;
use Enqueue\Null\NullMessage;
use Enqueue\ProcessorRegistryInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\ProcessorInterface;
use PHPUnit\Framework\TestCase;

class DelegateProcessorTest extends TestCase
{
    public function testCouldBeConstructedWithRequiredArguments()
    {
        new DelegateProcessor($this->createProcessorRegistryMock());
    }

    public function testShouldThrowExceptionIfProcessorNameIsNotSet()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Got message without required parameter: "enqueue.processor"');

        $processor = new DelegateProcessor($this->createProcessorRegistryMock());
        $processor->process(new NullMessage(), $this->createContextMock());
    }

    public function testShouldProcessMessage()
    {
        $session = $this->createContextMock();
        $message = new NullMessage();
        $message->setProperties([
            Config::PROCESSOR => 'processor-name',
        ]);

        $processor = $this->createProcessorMock();
        $processor
            ->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($message), $this->identicalTo($session))
            ->will($this->returnValue('return-value'))
        ;

        $processorRegistry = $this->createProcessorRegistryMock();
        $processorRegistry
            ->expects($this->once())
            ->method('get')
            ->with('processor-name')
            ->will($this->returnValue($processor))
        ;

        $processor = new DelegateProcessor($processorRegistry);
        $return = $processor->process($message, $session);

        $this->assertEquals('return-value', $return);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ProcessorRegistryInterface
     */
    protected function createProcessorRegistryMock()
    {
        return $this->createMock(ProcessorRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContextInterface
     */
    protected function createContextMock()
    {
        return $this->createMock(ContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ProcessorInterface
     */
    protected function createProcessorMock()
    {
        return $this->createMock(ProcessorInterface::class);
    }
}
