<?php

namespace Enqueue\AsyncEventDispatcher;

use Interop\Queue\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

interface EventTransformer
{
    /**
     * @param string     $eventName
     * @param Event|null $event
     *
     * @return MessageInterface
     */
    public function toMessage($eventName, Event $event);

    /**
     * If you able to transform message back to event return it.
     * If you failed to transform for some reason you can return a string status (@see Process constants) or an object that implements __toString method.
     * The object must have a __toString method is supposed to be used as Processor::process return value.
     *
     * @param string  $eventName
     * @param MessageInterface $message
     *
     * @return Event|string|object
     */
    public function toEvent($eventName, MessageInterface $message);
}
