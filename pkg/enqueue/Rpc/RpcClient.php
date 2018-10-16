<?php

namespace Enqueue\Rpc;

use Enqueue\Util\UUID;
use Interop\Queue\ContextInterface;
use Interop\Queue\DestinationInterface;
use Interop\Queue\MessageInterface as InteropMessage;

class RpcClient
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var RpcFactory
     */
    private $rpcFactory;

    /**
     * @param ContextInterface    $context
     * @param RpcFactory $promiseFactory
     */
    public function __construct(ContextInterface $context, RpcFactory $promiseFactory = null)
    {
        $this->context = $context;
        $this->rpcFactory = $promiseFactory ?: new RpcFactory($context);
    }

    /**
     * @param DestinationInterface    $destination
     * @param InteropMessage $message
     * @param int            $timeout
     *
     * @throws TimeoutException if the wait timeout is reached
     *
     * @return InteropMessage
     */
    public function call(DestinationInterface $destination, InteropMessage $message, $timeout)
    {
        return $this->callAsync($destination, $message, $timeout)->receive();
    }

    /**
     * @param DestinationInterface    $destination
     * @param InteropMessage $message
     * @param int            $timeout
     *
     * @return Promise
     */
    public function callAsync(DestinationInterface $destination, InteropMessage $message, $timeout)
    {
        if ($timeout < 1) {
            throw new \InvalidArgumentException(sprintf('Timeout must be positive not zero integer. Got %s', $timeout));
        }

        $deleteReplyQueue = false;
        $replyTo = $message->getReplyTo();

        if (false == $replyTo) {
            $message->setReplyTo($replyTo = $this->rpcFactory->createReplyTo());
            $deleteReplyQueue = true;
        }

        if (false == $message->getCorrelationId()) {
            $message->setCorrelationId(UUID::generate());
        }

        $this->context->createProducer()->send($destination, $message);

        $promise = $this->rpcFactory->createPromise($replyTo, $message->getCorrelationId(), $timeout);
        $promise->setDeleteReplyQueue($deleteReplyQueue);

        return $promise;
    }
}
