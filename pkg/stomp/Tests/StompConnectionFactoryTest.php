<?php

namespace Enqueue\Stomp\Tests;

use Enqueue\Stomp\StompConnectionFactory;
use Enqueue\Stomp\StompContext;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\ConnectionFactoryInterface;

class StompConnectionFactoryTest extends \PHPUnit\Framework\TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementConnectionFactoryInterface()
    {
        $this->assertClassImplements(ConnectionFactoryInterface::class, StompConnectionFactory::class);
    }

    public function testShouldCreateLazyContext()
    {
        $factory = new StompConnectionFactory(['lazy' => true]);

        $context = $factory->createContext();

        $this->assertInstanceOf(StompContext::class, $context);

        $this->assertAttributeEquals(null, 'stomp', $context);
        $this->assertInternalType('callable', $this->readAttribute($context, 'stompFactory'));
    }
}
