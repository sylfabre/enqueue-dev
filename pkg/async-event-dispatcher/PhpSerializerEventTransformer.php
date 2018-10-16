<?php

namespace Enqueue\AsyncEventDispatcher;

use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class PhpSerializerEventTransformer implements EventTransformer
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

    /**
     * {@inheritdoc}
     */
    public function toMessage($eventName, Event $event = null)
    {
        return $this->context->createMessage(serialize($event));
    }

    /**
     * {@inheritdoc}
     */
    public function toEvent($eventName, MessageInterface $message)
    {
        return unserialize($message->getBody());
    }
}
