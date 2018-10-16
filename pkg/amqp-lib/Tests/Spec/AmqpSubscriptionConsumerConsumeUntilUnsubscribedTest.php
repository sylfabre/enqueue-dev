<?php

namespace Enqueue\AmqpLib\Tests\Spec;

use Enqueue\AmqpLib\AmqpConnectionFactory;
use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpQueue;
use Interop\Queue\ContextInterface;
use Interop\Queue\Spec\SubscriptionConsumerConsumeUntilUnsubscribedSpec;

/**
 * @group functional
 */
class AmqpSubscriptionConsumerConsumeUntilUnsubscribedTest extends SubscriptionConsumerConsumeUntilUnsubscribedSpec
{
    protected function tearDown()
    {
        if ($this->subscriptionConsumer) {
            $this->subscriptionConsumer->unsubscribeAll();
        }

        parent::tearDown();
    }

    /**
     * @return AmqpContext
     *
     * {@inheritdoc}
     */
    protected function createContext()
    {
        $factory = new AmqpConnectionFactory(getenv('AMQP_DSN'));

        $context = $factory->createContext();
        $context->setQos(0, 1, false);

        return $context;
    }

    /**
     * @param AmqpContext $context
     *
     * {@inheritdoc}
     */
    protected function createQueue(ContextInterface $context, $queueName)
    {
        /** @var AmqpQueue $queue */
        $queue = parent::createQueue($context, $queueName);
        $context->declareQueue($queue);
        $context->purgeQueue($queue);

        return $queue;
    }
}
