<?php

namespace Enqueue\Consumption;

use Interop\Queue\ContextInterface;
use Interop\Queue\ProcessorInterface;
use Interop\Queue\QueueInterface as InteropQueue;

interface QueueConsumerInterface
{
    /**
     * In milliseconds.
     */
    public function setReceiveTimeout(int $timeout): void;

    /**
     * In milliseconds.
     */
    public function getReceiveTimeout(): int;

    public function getContext(): ContextInterface;

    /**
     * @param string|InteropQueue $queueName
     */
    public function bind($queueName, ProcessorInterface $processor): self;

    /**
     * @param string|InteropQueue $queueName
     * @param mixed               $queue
     */
    public function bindCallback($queue, callable $processor): self;

    /**
     * Runtime extension - is an extension or a collection of extensions which could be set on runtime.
     * Here's a good example: @see LimitsExtensionsCommandTrait.
     *
     * @param ExtensionInterface|null $runtimeExtension
     *
     * @throws \Exception
     */
    public function consume(ExtensionInterface $runtimeExtension = null): void;
}
