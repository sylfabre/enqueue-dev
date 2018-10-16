<?php

namespace Enqueue\Gearman\Tests\Spec;

use Enqueue\Gearman\GearmanConnectionFactory;
use Interop\Queue\ContextInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\Spec\SendToAndReceiveFromQueueSpec;

/**
 * @group functional
 */
class GearmanSendToAndReceiveFromQueueTest extends SendToAndReceiveFromQueueSpec
{
    /**
     * {@inheritdoc}
     */
    protected function createContext()
    {
        $factory = new GearmanConnectionFactory(getenv('GEARMAN_DSN'));

        return $factory->createContext();
    }

    /**
     * @param ContextInterface $context
     * @param string  $queueName
     *
     * @return QueueInterface
     */
    protected function createQueue(ContextInterface $context, $queueName)
    {
        return $context->createQueue($queueName.time());
    }
}
