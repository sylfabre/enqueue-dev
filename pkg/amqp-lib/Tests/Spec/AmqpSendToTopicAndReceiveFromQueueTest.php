<?php

namespace Enqueue\AmqpLib\Tests\Spec;

use Enqueue\AmqpLib\AmqpConnectionFactory;
use Enqueue\AmqpLib\AmqpContext;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Queue\ContextInterface;
use Interop\Queue\Spec\SendToTopicAndReceiveFromQueueSpec;

/**
 * @group functional
 */
class AmqpSendToTopicAndReceiveFromQueueTest extends SendToTopicAndReceiveFromQueueSpec
{
    /**
     * {@inheritdoc}
     */
    protected function createContext()
    {
        $factory = new AmqpConnectionFactory(getenv('AMQP_DSN'));

        return $factory->createContext();
    }

    /**
     * {@inheritdoc}
     *
     * @param AmqpContext $context
     */
    protected function createQueue(ContextInterface $context, $queueName)
    {
        $queue = $context->createQueue($queueName);
        $context->declareQueue($queue);
        $context->purgeQueue($queue);

        $context->bind(new AmqpBind($context->createTopic($queueName), $queue));

        return $queue;
    }

    /**
     * {@inheritdoc}
     *
     * @param AmqpContext $context
     */
    protected function createTopic(ContextInterface $context, $topicName)
    {
        $topic = $context->createTopic($topicName);
        $topic->setType(AmqpTopic::TYPE_FANOUT);
        $topic->addFlag(AmqpTopic::FLAG_DURABLE);
        $context->declareTopic($topic);

        return $topic;
    }
}
