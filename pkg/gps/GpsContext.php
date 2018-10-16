<?php

declare(strict_types=1);

namespace Enqueue\Gps;

use Google\Cloud\Core\Exception\ConflictException;
use Google\Cloud\PubSub\PubSubClient;
use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\PurgeQueueNotSupportedException;
use Interop\Queue\Exception\SubscriptionConsumerNotSupportedException;
use Interop\Queue\Exception\TemporaryQueueNotSupportedException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use Interop\Queue\TopicInterface;

class GpsContext implements ContextInterface
{
    /**
     * @var PubSubClient
     */
    private $client;

    /**
     * @var callable
     */
    private $clientFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * Callable must return instance of PubSubClient once called.
     *
     * @param PubSubClient|callable $client
     */
    public function __construct($client, array $options = [])
    {
        $this->options = array_replace([
            'ackDeadlineSeconds' => 10,
        ], $options);

        if ($client instanceof PubSubClient) {
            $this->client = $client;
        } elseif (is_callable($client)) {
            $this->clientFactory = $client;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The $client argument must be either %s or callable that returns %s once called.',
                PubSubClient::class,
                PubSubClient::class
            ));
        }
    }

    /**
     * @return GpsMessage
     */
    public function createMessage(string $body = '', array $properties = [], array $headers = []): MessageInterface
    {
        return new GpsMessage($body, $properties, $headers);
    }

    /**
     * @return GpsTopic
     */
    public function createTopic(string $topicName): TopicInterface
    {
        return new GpsTopic($topicName);
    }

    /**
     * @return GpsQueue
     */
    public function createQueue(string $queueName): QueueInterface
    {
        return new GpsQueue($queueName);
    }

    public function createTemporaryQueue(): QueueInterface
    {
        throw TemporaryQueueNotSupportedException::providerDoestNotSupportIt();
    }

    /**
     * @return GpsProducer
     */
    public function createProducer(): ProducerInterface
    {
        return new GpsProducer($this);
    }

    /**
     * @param GpsQueue|GpsTopic $destination
     *
     * @return GpsConsumer
     */
    public function createConsumer(DestinationInterface $destination): ConsumerInterface
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, GpsQueue::class);

        return new GpsConsumer($this, $destination);
    }

    public function close(): void
    {
    }

    public function createSubscriptionConsumer(): SubscriptionConsumerInterface
    {
        throw SubscriptionConsumerNotSupportedException::providerDoestNotSupportIt();
    }

    public function purgeQueue(QueueInterface $queue): void
    {
        throw PurgeQueueNotSupportedException::providerDoestNotSupportIt();
    }

    public function declareTopic(GpsTopic $topic): void
    {
        try {
            $this->getClient()->createTopic($topic->getTopicName());
        } catch (ConflictException $e) {
        }
    }

    public function subscribe(GpsTopic $topic, GpsQueue $queue): void
    {
        $this->declareTopic($topic);

        try {
            $this->getClient()->subscribe($queue->getQueueName(), $topic->getTopicName(), [
                'ackDeadlineSeconds' => $this->options['ackDeadlineSeconds'],
            ]);
        } catch (ConflictException $e) {
        }
    }

    public function getClient(): PubSubClient
    {
        if (false == $this->client) {
            $client = call_user_func($this->clientFactory);
            if (false == $client instanceof PubSubClient) {
                throw new \LogicException(sprintf(
                    'The factory must return instance of %s. It returned %s',
                    PubSubClient::class,
                    is_object($client) ? get_class($client) : gettype($client)
                ));
            }

            $this->client = $client;
        }

        return $this->client;
    }
}
