<?php

declare(strict_types=1);

namespace Enqueue\Sqs;

use Aws\Sqs\SqsClient;
use Interop\Queue\ConsumerInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\SubscriptionConsumerNotSupportedException;
use Interop\Queue\Exception\TemporaryQueueNotSupportedException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use Interop\Queue\QueueInterface;
use Interop\Queue\SubscriptionConsumerInterface;
use Interop\Queue\TopicInterface;

class SqsContext implements ContextInterface
{
    /**
     * @var SqsClient
     */
    private $client;

    /**
     * @var callable
     */
    private $clientFactory;

    /**
     * @var array
     */
    private $queueUrls;

    /**
     * Callable must return instance of SqsClient once called.
     *
     * @param SqsClient|callable $client
     */
    public function __construct($client)
    {
        if ($client instanceof SqsClient) {
            $this->client = $client;
        } elseif (is_callable($client)) {
            $this->clientFactory = $client;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The $client argument must be either %s or callable that returns %s once called.',
                SqsClient::class,
                SqsClient::class
            ));
        }
    }

    /**
     * @return SqsMessage
     */
    public function createMessage(string $body = '', array $properties = [], array $headers = []): MessageInterface
    {
        return new SqsMessage($body, $properties, $headers);
    }

    /**
     * @return SqsDestination
     */
    public function createTopic(string $topicName): TopicInterface
    {
        return new SqsDestination($topicName);
    }

    /**
     * @return SqsDestination
     */
    public function createQueue(string $queueName): QueueInterface
    {
        return new SqsDestination($queueName);
    }

    public function createTemporaryQueue(): QueueInterface
    {
        throw TemporaryQueueNotSupportedException::providerDoestNotSupportIt();
    }

    /**
     * @return SqsProducer
     */
    public function createProducer(): ProducerInterface
    {
        return new SqsProducer($this);
    }

    /**
     * @param SqsDestination $destination
     *
     * @return SqsConsumer
     */
    public function createConsumer(DestinationInterface $destination): ConsumerInterface
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, SqsDestination::class);

        return new SqsConsumer($this, $destination);
    }

    public function close(): void
    {
    }

    /**
     * @param SqsDestination $queue
     */
    public function purgeQueue(QueueInterface $queue): void
    {
        InvalidDestinationException::assertDestinationInstanceOf($queue, SqsDestination::class);

        $this->getClient()->purgeQueue([
            'QueueUrl' => $this->getQueueUrl($queue),
        ]);
    }

    public function createSubscriptionConsumer(): SubscriptionConsumerInterface
    {
        throw SubscriptionConsumerNotSupportedException::providerDoestNotSupportIt();
    }

    public function getClient(): SqsClient
    {
        if (false == $this->client) {
            $client = call_user_func($this->clientFactory);
            if (false == $client instanceof SqsClient) {
                throw new \LogicException(sprintf(
                    'The factory must return instance of "%s". But it returns %s',
                    SqsClient::class,
                    is_object($client) ? get_class($client) : gettype($client)
                ));
            }

            $this->client = $client;
        }

        return $this->client;
    }

    public function getQueueUrl(SqsDestination $destination): string
    {
        if (isset($this->queueUrls[$destination->getQueueName()])) {
            return $this->queueUrls[$destination->getQueueName()];
        }

        $result = $this->getClient()->getQueueUrl([
            'QueueName' => $destination->getQueueName(),
        ]);

        if (false == $result->hasKey('QueueUrl')) {
            throw new \RuntimeException(sprintf('QueueUrl cannot be resolved. queueName: "%s"', $destination->getQueueName()));
        }

        return $this->queueUrls[$destination->getQueueName()] = $result->get('QueueUrl');
    }

    public function declareQueue(SqsDestination $dest): void
    {
        $result = $this->getClient()->createQueue([
            'Attributes' => $dest->getAttributes(),
            'QueueName' => $dest->getQueueName(),
        ]);

        if (false == $result->hasKey('QueueUrl')) {
            throw new \RuntimeException(sprintf('Cannot create queue. queueName: "%s"', $dest->getQueueName()));
        }

        $this->queueUrls[$dest->getQueueName()] = $result->get('QueueUrl');
    }

    public function deleteQueue(SqsDestination $dest): void
    {
        $this->getClient()->deleteQueue([
            'QueueUrl' => $this->getQueueUrl($dest),
        ]);

        unset($this->queueUrls[$dest->getQueueName()]);
    }
}
