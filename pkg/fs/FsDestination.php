<?php

declare(strict_types=1);

namespace Enqueue\Fs;

use Interop\Queue\QueueInterface;
use Interop\Queue\TopicInterface;

class FsDestination implements QueueInterface, TopicInterface
{
    /**
     * @var \SplFileInfo
     */
    private $file;

    public function __construct(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getFileInfo(): \SplFileInfo
    {
        return $this->file;
    }

    public function getName(): string
    {
        return $this->file->getFilename();
    }

    public function getQueueName(): string
    {
        return $this->file->getFilename();
    }

    public function getTopicName(): string
    {
        return $this->file->getFilename();
    }
}
