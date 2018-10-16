<?php

namespace Enqueue\Router;

use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\ProcessorInterface;

class RouteRecipientListProcessor implements ProcessorInterface
{
    /**
     * @var RecipientListRouterInterface
     */
    private $router;

    /**
     * @param RecipientListRouterInterface $router
     */
    public function __construct(RecipientListRouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function process(InteropMessage $message, ContextInterface $context)
    {
        $producer = $context->createProducer();
        foreach ($this->router->route($message) as $recipient) {
            $producer->send($recipient->getDestination(), $recipient->getMessage());
        }

        return self::ACK;
    }
}
