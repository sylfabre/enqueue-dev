<?php

namespace Enqueue\Bundle\Tests\Functional\App;

use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProcessorInterface;

class TestExclusiveCommandSubscriberProcessor implements ProcessorInterface, CommandSubscriberInterface
{
    public $calls = [];

    public function process(MessageInterface $message, ContextInterface $context)
    {
        $this->calls[] = $message;

        return Result::ACK;
    }

    public static function getSubscribedCommand()
    {
        return [
            'command' => 'theExclusiveCommandName',
            'processor' => 'theExclusiveCommandName',
            'queue' => 'the_exclusive_command_queue',
            'prefix_queue' => true,
            'exclusive' => true,
        ];
    }
}
