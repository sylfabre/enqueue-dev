<?php

declare(strict_types=1);

namespace Enqueue\Null;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\SubscriptionConsumerInterface;

class NullSubscriptionConsumer implements SubscriptionConsumerInterface
{
    public function consume(int $timeout = 0): void
    {
    }

    public function subscribe(ConsumerInterface $consumer, callable $callback): void
    {
    }

    public function unsubscribe(ConsumerInterface $consumer): void
    {
    }

    public function unsubscribeAll(): void
    {
    }
}
