<?php

namespace Enqueue\Client;

use Enqueue\ProcessorRegistryInterface;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\ProcessorInterface;

class DelegateProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorRegistryInterface
     */
    private $registry;

    /**
     * @param ProcessorRegistryInterface $registry
     */
    public function __construct(ProcessorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function process(InteropMessage $message, ContextInterface $context)
    {
        $processorName = $message->getProperty(Config::PROCESSOR);
        if (false == $processorName) {
            throw new \LogicException(sprintf(
                'Got message without required parameter: "%s"',
                Config::PROCESSOR
            ));
        }

        return $this->registry->get($processorName)->process($message, $context);
    }
}
