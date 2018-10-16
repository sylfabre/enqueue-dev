<?php

declare(strict_types=1);

namespace Enqueue\Redis;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\MessageInterface;
use Interop\Queue\QueueInterface;

class RedisConsumer implements ConsumerInterface
{
    /**
     * @var RedisDestination
     */
    private $queue;

    /**
     * @var RedisContext
     */
    private $context;

    public function __construct(RedisContext $context, RedisDestination $queue)
    {
        $this->context = $context;
        $this->queue = $queue;
    }

    /**
     * @return RedisDestination
     */
    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }

    /**
     * @return RedisMessage
     */
    public function receive(int $timeout = 0): ?MessageInterface
    {
        $timeout = (int) ($timeout / 1000);
        if (empty($timeout)) {
            while (true) {
                if ($message = $this->receive(5000)) {
                    return $message;
                }
            }
        }

        if ($result = $this->getRedis()->brpop([$this->queue->getName()], $timeout)) {
            return RedisMessage::jsonUnserialize($result->getMessage());
        }

        return null;
    }

    /**
     * @return RedisMessage
     */
    public function receiveNoWait(): ?MessageInterface
    {
        if ($result = $this->getRedis()->rpop($this->queue->getName())) {
            return RedisMessage::jsonUnserialize($result->getMessage());
        }

        return null;
    }

    /**
     * @param RedisMessage $message
     */
    public function acknowledge(MessageInterface $message): void
    {
        // do nothing. redis transport always works in auto ack mode
    }

    /**
     * @param RedisMessage $message
     */
    public function reject(MessageInterface $message, bool $requeue = false): void
    {
        InvalidMessageException::assertMessageInstanceOf($message, RedisMessage::class);

        // do nothing on reject. redis transport always works in auto ack mode

        if ($requeue) {
            $this->context->createProducer()->send($this->queue, $message);
        }
    }

    private function getRedis(): Redis
    {
        return $this->context->getRedis();
    }
}
