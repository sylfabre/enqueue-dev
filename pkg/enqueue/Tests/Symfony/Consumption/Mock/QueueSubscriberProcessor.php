<?php

namespace Enqueue\Tests\Symfony\Consumption\Mock;

use Enqueue\Consumption\QueueSubscriberInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\ProcessorInterface;

class QueueSubscriberProcessor implements ProcessorInterface, QueueSubscriberInterface
{
    public function process(InteropMessage $message, ContextInterface $context)
    {
    }

    public static function getSubscribedQueues()
    {
        return ['fooSubscribedQueues', 'barSubscribedQueues'];
    }
}
