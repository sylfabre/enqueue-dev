<?php

declare(strict_types=1);

namespace Enqueue\Stomp;

use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\PurgeQueueNotSupportedException;
use Interop\Queue\Exception\SubscriptionConsumerNotSupportedException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use Interop\Queue\TopicInterface;

class StompContext implements ContextInterface
{
    /**
     * @var BufferedStompClient
     */
    private $stomp;

    /**
     * @var callable
     */
    private $stompFactory;

    /**
     * @param BufferedStompClient|callable $stomp
     */
    public function __construct($stomp)
    {
        if ($stomp instanceof BufferedStompClient) {
            $this->stomp = $stomp;
        } elseif (is_callable($stomp)) {
            $this->stompFactory = $stomp;
        } else {
            throw new \InvalidArgumentException('The stomp argument must be either BufferedStompClient or callable that return BufferedStompClient.');
        }
    }

    /**
     * @return StompMessage
     */
    public function createMessage(string $body = '', array $properties = [], array $headers = []): MessageInterface
    {
        return new StompMessage($body, $properties, $headers);
    }

    /**
     * @return StompDestination
     */
    public function createQueue(string $name): QueueInterface
    {
        if (0 !== strpos($name, '/')) {
            $destination = new StompDestination();
            $destination->setType(StompDestination::TYPE_QUEUE);
            $destination->setStompName($name);

            return $destination;
        }

        return $this->createDestination($name);
    }

    /**
     * @return StompDestination
     */
    public function createTemporaryQueue(): QueueInterface
    {
        $queue = $this->createQueue(uniqid('', true));
        $queue->setType(StompDestination::TYPE_TEMP_QUEUE);

        return $queue;
    }

    /**
     * @return StompDestination
     */
    public function createTopic(string $name): TopicInterface
    {
        if (0 !== strpos($name, '/')) {
            $destination = new StompDestination();
            $destination->setType(StompDestination::TYPE_EXCHANGE);
            $destination->setStompName($name);

            return $destination;
        }

        return $this->createDestination($name);
    }

    public function createDestination(string $destination): StompDestination
    {
        $types = [
            StompDestination::TYPE_TOPIC,
            StompDestination::TYPE_EXCHANGE,
            StompDestination::TYPE_QUEUE,
            StompDestination::TYPE_AMQ_QUEUE,
            StompDestination::TYPE_TEMP_QUEUE,
            StompDestination::TYPE_REPLY_QUEUE,
        ];

        $dest = $destination;
        $type = null;
        $name = null;
        $routingKey = null;

        foreach ($types as $_type) {
            $typePrefix = '/'.$_type.'/';
            if (0 === strpos($dest, $typePrefix)) {
                $type = $_type;
                $dest = substr($dest, strlen($typePrefix));

                break;
            }
        }

        if (null === $type) {
            throw new \LogicException(sprintf('Destination name is invalid, cant find type: "%s"', $destination));
        }

        $pieces = explode('/', $dest);

        if (count($pieces) > 2) {
            throw new \LogicException(sprintf('Destination name is invalid, found extra / char: "%s"', $destination));
        }

        if (empty($pieces[0])) {
            throw new \LogicException(sprintf('Destination name is invalid, name is empty: "%s"', $destination));
        }

        $name = $pieces[0];

        if (isset($pieces[1])) {
            if (empty($pieces[1])) {
                throw new \LogicException(sprintf('Destination name is invalid, routing key is empty: "%s"', $destination));
            }

            $routingKey = $pieces[1];
        }

        $destination = new StompDestination();
        $destination->setType($type);
        $destination->setStompName($name);
        $destination->setRoutingKey($routingKey);

        return $destination;
    }

    /**
     * @param StompDestination $destination
     *
     * @return StompConsumer
     */
    public function createConsumer(DestinationInterface $destination): ConsumerInterface
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, StompDestination::class);

        return new StompConsumer($this->getStomp(), $destination);
    }

    /**
     * @return StompProducer
     */
    public function createProducer(): ProducerInterface
    {
        return new StompProducer($this->getStomp());
    }

    public function close(): void
    {
        $this->getStomp()->disconnect();
    }

    public function createSubscriptionConsumer(): SubscriptionConsumerInterface
    {
        throw SubscriptionConsumerNotSupportedException::providerDoestNotSupportIt();
    }

    public function purgeQueue(QueueInterface $queue): void
    {
        throw PurgeQueueNotSupportedException::providerDoestNotSupportIt();
    }

    public function getStomp(): BufferedStompClient
    {
        if (false == $this->stomp) {
            $stomp = call_user_func($this->stompFactory);
            if (false == $stomp instanceof BufferedStompClient) {
                throw new \LogicException(sprintf(
                    'The factory must return instance of BufferedStompClient. It returns %s',
                    is_object($stomp) ? get_class($stomp) : gettype($stomp)
                ));
            }

            $this->stomp = $stomp;
        }

        return $this->stomp;
    }
}
