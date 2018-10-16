<?php

namespace Enqueue\Redis\Tests\Spec;

use Enqueue\Redis\RedisContext;
use Enqueue\Redis\RedisDestination;
use Enqueue\Test\RedisExtension;
use Interop\Queue\ContextInterface;
use Interop\Queue\Spec\SubscriptionConsumerConsumeFromAllSubscribedQueuesSpec;

/**
 * @group functional
 * @group Redis
 */
class RedisSubscriptionConsumerConsumeFromAllSubscribedQueuesTest extends SubscriptionConsumerConsumeFromAllSubscribedQueuesSpec
{
    use RedisExtension;

    /**
     * @return RedisContext
     *
     * {@inheritdoc}
     */
    protected function createContext()
    {
        return $this->buildPhpRedisContext();
    }

    /**
     * @param RedisContext $context
     *
     * {@inheritdoc}
     */
    protected function createQueue(ContextInterface $context, $queueName)
    {
        /** @var RedisDestination $queue */
        $queue = parent::createQueue($context, $queueName);
        $context->getRedis()->del($queueName);

        return $queue;
    }
}
