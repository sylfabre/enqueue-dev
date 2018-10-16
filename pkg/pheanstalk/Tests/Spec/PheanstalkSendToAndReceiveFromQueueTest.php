<?php

namespace Enqueue\Pheanstalk\Tests\Spec;

use Enqueue\Pheanstalk\PheanstalkConnectionFactory;
use Interop\Queue\ContextInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\Spec\SendToAndReceiveFromQueueSpec;

/**
 * @group functional
 */
class PheanstalkSendToAndReceiveFromQueueTest extends SendToAndReceiveFromQueueSpec
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
