<?php

declare(strict_types=1);

namespace Enqueue\Stomp;

use Interop\Queue\DestinationInterface;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\MessageInterface;
use Interop\Queue\ProducerInterface;
use Stomp\Client;
use Stomp\Transport\Message as StompLibMessage;

class StompProducer implements ProducerInterface
{
    /**
     * @var Client
     */
    private $stomp;

    /**
     * @param Client $stomp
     */
    public function __construct(Client $stomp)
    {
        $this->stomp = $stomp;
    }

    /**
     * @param StompDestination $destination
     * @param StompMessage     $message
     */
    public function send(DestinationInterface $destination, MessageInterface $message): void
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, StompDestination::class);
        InvalidMessageException::assertMessageInstanceOf($message, StompMessage::class);

        $headers = array_merge($message->getHeaders(), $destination->getHeaders());
        $headers = StompHeadersEncoder::encode($headers, $message->getProperties());

        $stompMessage = new StompLibMessage($message->getBody(), $headers);

        $this->stomp->send($destination->getQueueName(), $stompMessage);
    }

    public function setDeliveryDelay(int $deliveryDelay = null): ProducerInterface
    {
        if (null === $deliveryDelay) {
            return $this;
        }

        throw new \LogicException('Not implemented');
    }

    public function getDeliveryDelay(): ?int
    {
        return null;
    }

    public function setPriority(int $priority = null): ProducerInterface
    {
        if (null === $priority) {
            return $this;
        }

        throw new \LogicException('Not implemented');
    }

    public function getPriority(): ?int
    {
        return null;
    }

    public function setTimeToLive(int $timeToLive = null): ProducerInterface
    {
        if (null === $timeToLive) {
            return $this;
        }

        throw new \LogicException('Not implemented');
    }

    public function getTimeToLive(): ?int
    {
        return null;
    }
}
