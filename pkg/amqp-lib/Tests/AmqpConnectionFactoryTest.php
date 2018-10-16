<?php

namespace Enqueue\AmqpLib\Tests;

use Enqueue\AmqpLib\AmqpConnectionFactory;
use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Enqueue\Test\ClassExtensionTrait;
use Interop\Queue\ConnectionFactoryInterface;
use PHPUnit\Framework\TestCase;

class AmqpConnectionFactoryTest extends TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementConnectionFactoryInterface()
    {
        $this->assertClassImplements(ConnectionFactoryInterface::class, AmqpConnectionFactory::class);
    }

    public function testShouldSetRabbitMqDlxDelayStrategyIfRabbitMqSchemeExtensionPresent()
    {
        $factory = new AmqpConnectionFactory('amqp+rabbitmq:');

        $this->assertAttributeInstanceOf(RabbitMqDlxDelayStrategy::class, 'delayStrategy', $factory);
    }
}
