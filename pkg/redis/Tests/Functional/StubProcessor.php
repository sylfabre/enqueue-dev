<?php

namespace Enqueue\Redis\Tests\Functional;

use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProcessorInterface;

class StubProcessor implements ProcessorInterface
{
    public $result = self::ACK;

    /** @var MessageInterface */
    public $lastProcessedMessage;

    public function process(MessageInterface $message, ContextInterface $context)
    {
        $this->lastProcessedMessage = $message;

        return $this->result;
    }
}
