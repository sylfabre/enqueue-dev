<?php

namespace Enqueue\Bundle\Tests\Functional\App;

use Enqueue\AsyncEventDispatcher\EventTransformer;
use Enqueue\Util\JSON;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

class TestAsyncEventTransformer implements EventTransformer
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @param ContextInterface $context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    public function toMessage($eventName, Event $event)
    {
        if (Event::class === get_class($event)) {
            return $this->context->createMessage(json_encode(''));
        }

        /** @var GenericEvent $event */
        if (false == $event instanceof GenericEvent) {
            throw new \LogicException('Must be GenericEvent');
        }

        return $this->context->createMessage(json_encode([
            'subject' => $event->getSubject(),
            'arguments' => $event->getArguments(),
        ]));
    }

    public function toEvent($eventName, MessageInterface $message)
    {
        $data = JSON::decode($message->getBody());

        if ('' === $data) {
            return new Event();
        }

        return new GenericEvent($data['subject'], $data['arguments']);
    }
}
