<?php

declare(strict_types=1);

namespace Enqueue\Sqs;

use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\Exception\PriorityNotSupportedException;
use Interop\Queue\Exception\TimeToLiveNotSupportedException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;

class SqsProducer implements ProducerInterface
{
    /**
     * @var int|null
     */
    private $deliveryDelay;

    /**
     * @var SqsContext
     */
    private $context;

    public function __construct(SqsContext $context)
    {
        $this->context = $context;
    }

    /**
     * @param SqsDestination $destination
     * @param SqsMessage     $message
     */
    public function send(DestinationInterface $destination, MessageInterface $message): void
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, SqsDestination::class);
        InvalidMessageException::assertMessageInstanceOf($message, SqsMessage::class);

        $body = $message->getBody();
        if (empty($body)) {
            throw new InvalidMessageException('The message body must be a non-empty string.');
        }

        $arguments = [
            'MessageAttributes' => [
                'Headers' => [
                    'DataType' => 'String',
                    'StringValue' => json_encode([$message->getHeaders(), $message->getProperties()]),
                ],
            ],
            'MessageBody' => $body,
            'QueueUrl' => $this->context->getQueueUrl($destination),
        ];

        if (null !== $this->deliveryDelay) {
            $arguments['DelaySeconds'] = (int) $this->deliveryDelay / 1000;
        }

        if ($message->getDelaySeconds()) {
            $arguments['DelaySeconds'] = $message->getDelaySeconds();
        }

        if ($message->getMessageDeduplicationId()) {
            $arguments['MessageDeduplicationId'] = $message->getMessageDeduplicationId();
        }

        if ($message->getMessageGroupId()) {
            $arguments['MessageGroupId'] = $message->getMessageGroupId();
        }

        $result = $this->context->getClient()->sendMessage($arguments);

        if (false == $result->hasKey('MessageId')) {
            throw new \RuntimeException('Message was not sent');
        }
    }

    /**
     * @return SqsProducer
     */
    public function setDeliveryDelay(int $deliveryDelay = null): ProducerInterface
    {
        $this->deliveryDelay = $deliveryDelay;

        return $this;
    }

    public function getDeliveryDelay(): ?int
    {
        return $this->deliveryDelay;
    }

    /**
     * @return SqsProducer
     */
    public function setPriority(int $priority = null): ProducerInterface
    {
        if (null === $priority) {
            return $this;
        }

        throw PriorityNotSupportedException::providerDoestNotSupportIt();
    }

    public function getPriority(): ?int
    {
        return null;
    }

    /**
     * @return SqsProducer
     */
    public function setTimeToLive(int $timeToLive = null): ProducerInterface
    {
        if (null === $timeToLive) {
            return $this;
        }

        throw TimeToLiveNotSupportedException::providerDoestNotSupportIt();
    }

    public function getTimeToLive(): ?int
    {
        return null;
    }
}
