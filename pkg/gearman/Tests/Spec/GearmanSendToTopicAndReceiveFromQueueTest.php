<?php

namespace Enqueue\Gearman\Tests\Spec;

use Enqueue\Gearman\GearmanConnectionFactory;
use Interop\Queue\ContextInterface;
use Interop\Queue\Spec\SendToTopicAndReceiveFromQueueSpec;

/**
 * @group functional
 */
class GearmanSendToTopicAndReceiveFromQueueTest extends SendToTopicAndReceiveFromQueueSpec
{
    private $time;

    public function setUp()
    {
        $this->time = time();
    }

    /**
     * {@inheritdoc}
     */
    protected function createContext()
    {
        $factory = new GearmanConnectionFactory(getenv('GEARMAN_DSN'));

        return $factory->createContext();
    }

    /**
     * {@inheritdoc}
     */
    protected function createQueue(ContextInterface $context, $queueName)
    {
        return $context->createQueue($queueName.$this->time);
    }

    /**
     * {@inheritdoc}
     */
    protected function createTopic(ContextInterface $context, $topicName)
    {
        return $context->createTopic($topicName.$this->time);
    }
}
