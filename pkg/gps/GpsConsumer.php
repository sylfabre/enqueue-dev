<?php

declare(strict_types=1);

namespace Enqueue\Gps;

use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\PubSub\Message as GoogleMessage;
use Google\Cloud\PubSub\Subscription;
use Interop\Queue\ConsumerInterface;
use Interop\Queue\MessageInterface;
use Interop\Queue\QueueInterface;

class GpsConsumer implements ConsumerInterface
{
    /**
     * @var GpsContext
     */
    private $context;

    /**
     * @var GpsQueue
     */
    private $queue;

    /**
     * @var Subscription
     */
    private $subscription;

    public function __construct(GpsContext $context, GpsQueue $queue)
    {
        $this->context = $context;
        $this->queue = $queue;
    }

    /**
     * @return GpsQueue
     */
    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }

    /**
     * @return GpsMessage
     */
    public function receive(int $timeout = 0): ?MessageInterface
    {
        if (0 === $timeout) {
            while (true) {
                if ($message = $this->receiveMessage($timeout)) {
                    return $message;
                }
            }
        } else {
            return $this->receiveMessage($timeout);
        }
    }

    /**
     * @return GpsMessage
     */
    public function receiveNoWait(): ?MessageInterface
    {
        $messages = $this->getSubscription()->pull([
            'maxMessages' => 1,
            'returnImmediately' => true,
        ]);

        if ($messages) {
            return $this->convertMessage(current($messages));
        }

        return null;
    }

    /**
     * @param GpsMessage $message
     */
    public function acknowledge(MessageInterface $message): void
    {
        if (false == $message->getNativeMessage()) {
            throw new \LogicException('Native google pub/sub message required but it is empty');
        }

        $this->getSubscription()->acknowledge($message->getNativeMessage());
    }

    /**
     * @param GpsMessage $message
     */
    public function reject(MessageInterface $message, bool $requeue = false): void
    {
        if (false == $message->getNativeMessage()) {
            throw new \LogicException('Native google pub/sub message required but it is empty');
        }

        $this->getSubscription()->acknowledge($message->getNativeMessage());
    }

    private function getSubscription(): Subscription
    {
        if (null === $this->subscription) {
            $this->subscription = $this->context->getClient()->subscription($this->queue->getQueueName());
        }

        return $this->subscription;
    }

    private function convertMessage(GoogleMessage $message): GpsMessage
    {
        $gpsMessage = GpsMessage::jsonUnserialize($message->data());
        $gpsMessage->setNativeMessage($message);

        return $gpsMessage;
    }

    private function receiveMessage(int $timeout): ?GpsMessage
    {
        $timeout /= 1000;

        try {
            $messages = $this->getSubscription()->pull([
                'maxMessages' => 1,
                'requestTimeout' => $timeout,
            ]);

            if ($messages) {
                return $this->convertMessage(current($messages));
            }
        } catch (ServiceException $e) {
        } // timeout

        return null;
    }
}
