<?php

namespace Enqueue\Bundle\Tests\Unit\Consumption\Extension;

use Doctrine\Common\Persistence\ObjectManager;
use Enqueue\Bundle\Consumption\Extension\DoctrineClearIdentityMapExtension;
use Enqueue\Consumption\Context\MessageReceived;
use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface as InteropContext;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProcessorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineClearIdentityMapExtensionTest extends TestCase
{
    public function testCouldBeConstructedWithRequiredArguments()
    {
        new DoctrineClearIdentityMapExtension($this->createRegistryMock());
    }

    public function testShouldClearIdentityMap()
    {
        $manager = $this->createManagerMock();
        $manager
            ->expects($this->once())
            ->method('clear')
        ;

        $registry = $this->createRegistryMock();
        $registry
            ->expects($this->once())
            ->method('getManagers')
            ->will($this->returnValue(['manager-name' => $manager]))
        ;

        $context = $this->createContext();
        $context->getLogger()
            ->expects($this->once())
            ->method('debug')
            ->with('[DoctrineClearIdentityMapExtension] Clear identity map for manager "manager-name"')
        ;

        $extension = new DoctrineClearIdentityMapExtension($registry);
        $extension->onMessageReceived($context);
    }

    protected function createContext(): MessageReceived
    {
        return new MessageReceived(
            $this->createMock(InteropContext::class),
            $this->createMock(ConsumerInterface::class),
            $this->createMock(MessageInterface::class),
            $this->createMock(ProcessorInterface::class),
            1,
            $this->createMock(LoggerInterface::class)
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    protected function createRegistryMock(): RegistryInterface
    {
        return $this->createMock(RegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    protected function createManagerMock(): ObjectManager
    {
        return $this->createMock(ObjectManager::class);
    }
}
