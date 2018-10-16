<?php

namespace Enqueue;

use Interop\Queue\ProcessorInterface;

class ArrayProcessorRegistry implements ProcessorRegistryInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = [];
        array_walk($processors, function (ProcessorInterface $processor, string $key) {
            $this->processors[$key] = $processor;
        });
    }

    public function add(string $name, ProcessorInterface $processor): void
    {
        $this->processors[$name] = $processor;
    }

    public function get(string $processorName): ProcessorInterface
    {
        if (false == isset($this->processors[$processorName])) {
            throw new \LogicException(sprintf('Processor was not found. processorName: "%s"', $processorName));
        }

        return $this->processors[$processorName];
    }
}
