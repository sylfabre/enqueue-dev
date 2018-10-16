<?php

namespace Enqueue\Consumption;

use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\ProcessorInterface;

class CallbackProcessor implements ProcessorInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function process(InteropMessage $message, ContextInterface $context)
    {
        return call_user_func($this->callback, $message, $context);
    }
}
