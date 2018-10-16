<?php

namespace Enqueue\AmqpBunny\Tests\Spec;

use Enqueue\AmqpBunny\AmqpConnectionFactory;
use Enqueue\AmqpBunny\AmqpContext;
use Enqueue\AmqpTools\RabbitMqDelayPluginDelayStrategy;
use Interop\Queue\ContextInterface;
use Interop\Queue\Spec\SendAndReceiveDelayedMessageFromQueueSpec;

/**
 * @group functional
 */
class AmqpSendAndReceiveDelayedMessageWithDelayPluginStrategyTest extends SendAndReceiveDelayedMessageFromQueueSpec
{
    public function test()
    {
        $this->markTestIncomplete();
    }

    /**
     * {@inheritdoc}
     */
    protected function createContext()
    {
        $factory = new AmqpConnectionFactory(getenv('AMQP_DSN'));
        $factory->setDelayStrategy(new RabbitMqDelayPluginDelayStrategy());

        return $factory->createContext();
    }

    /**
     * @param AmqpContext $context
     *
     * {@inheritdoc}
     */
    protected function createQueue(ContextInterface $context, $queueName)
    {
        $queue = parent::createQueue($context, $queueName);

        $context->declareQueue($queue);
        $context->purgeQueue($queue);

        return $queue;
    }
}
