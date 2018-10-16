<?php

namespace Enqueue\Bundle\Tests\Functional\App;

use Enqueue\Client\TopicSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProcessorInterface;

class TestTopicSubscriberProcessor implements ProcessorInterface, TopicSubscriberInterface
{
    public $calls = [];

    public function process(MessageInterface $message, ContextInterface $context)
    {
        $this->calls[] = $message;

        return Result::reply(
            $context->createMessage($message->getBody().'Reply')
        );
    }

    public static function getSubscribedTopics()
    {
        return 'theTopic';
    }
}
