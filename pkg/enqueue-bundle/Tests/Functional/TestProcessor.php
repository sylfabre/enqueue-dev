<?php

namespace Enqueue\Bundle\Tests\Functional;

use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProcessorInterface;

class TestProcessor implements ProcessorInterface, TopicSubscriberInterface
{
    const TOPIC = 'test-topic';

    /**
     * @var MessageInterface
     */
    public $message;

    public function process(MessageInterface $message, ContextInterface $context)
    {
        $this->message = $message;

        return self::ACK;
    }

    public static function getSubscribedTopics()
    {
        return [self::TOPIC];
    }
}
