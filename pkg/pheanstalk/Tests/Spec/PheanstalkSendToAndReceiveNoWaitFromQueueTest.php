<?php

namespace Enqueue\Pheanstalk\Tests\Spec;

use Enqueue\Pheanstalk\PheanstalkConnectionFactory;
use Interop\Queue\ContextInterface;
use Interop\Queue\Spec\SendToAndReceiveNoWaitFromQueueSpec;

/**
 * @group functional
 */
class PheanstalkSendToAndReceiveNoWaitFromQueueTest extends SendToAndReceiveNoWaitFromQueueSpec
{
    /**
     * {@inheritdoc}
     */
    protected function createContext()
    {
        $factory = new PheanstalkConnectionFactory(getenv('BEANSTALKD_DSN'));

        return $factory->createContext();
    }

    /**
     * {@inheritdoc}
     */
    protected function createQueue(ContextInterface $context, $queueName)
    {
        return $context->createQueue($queueName.time());
    }
}
