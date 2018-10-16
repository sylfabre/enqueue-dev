<?php

namespace Enqueue\AsyncEventDispatcher;

use Interop\Queue\ContextInterface;
use Interop\Queue\QueueInterface;
use Symfony\Component\EventDispatcher\Event;

class AsyncListener
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var QueueInterface
     */
    private $eventQueue;

    /**
     * @var bool
     */
    private $syncMode;

    /**
     * @param ContextInterface      $context
     * @param Registry     $registry
     * @param QueueInterface|string $eventQueue
     */
    public function __construct(ContextInterface $context, Registry $registry, $eventQueue)
    {
        $this->context = $context;
        $this->registry = $registry;
        $this->eventQueue = $eventQueue instanceof QueueInterface ? $eventQueue : $context->createQueue($eventQueue);
    }

    public function __invoke(Event $event, $eventName)
    {
        $this->onEvent($event, $eventName);
    }

    public function resetSyncMode()
    {
        $this->syncMode = [];
    }

    /**
     * @param string $eventName
     */
    public function syncMode($eventName)
    {
        $this->syncMode[$eventName] = true;
    }

    /**
     * @param string $eventName
     *
     * @return bool
     */
    public function isSyncMode($eventName)
    {
        return isset($this->syncMode[$eventName]);
    }

    /**
     * @param Event  $event
     * @param string $eventName
     */
    public function onEvent(Event $event, $eventName)
    {
        if (false == isset($this->syncMode[$eventName])) {
            $transformerName = $this->registry->getTransformerNameForEvent($eventName);

            $message = $this->registry->getTransformer($transformerName)->toMessage($eventName, $event);
            $message->setProperty('event_name', $eventName);
            $message->setProperty('transformer_name', $transformerName);

            $this->context->createProducer()->send($this->eventQueue, $message);
        }
    }
}
