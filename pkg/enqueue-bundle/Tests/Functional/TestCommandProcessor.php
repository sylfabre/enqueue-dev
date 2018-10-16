<?php

namespace Enqueue\Bundle\Tests\Functional;

use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProcessorInterface;

class TestCommandProcessor implements ProcessorInterface, CommandSubscriberInterface
{
    const COMMAND = 'test-command';

    /**
     * @var MessageInterface
     */
    public $message;

    public function process(MessageInterface $message, ContextInterface $context)
    {
        $this->message = $message;

        return self::ACK;
    }

    public static function getSubscribedCommand()
    {
        return self::COMMAND;
    }
}
