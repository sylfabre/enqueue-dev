<?php

namespace Enqueue\Router;

use Interop\Queue\DestinationInterface;
use Interop\Queue\MessageInterface as InteropMessage;

class Recipient
{
    /**
     * @var DestinationInterface
     */
    private $destination;

    /**
     * @var InteropMessage
     */
    private $message;

    /**
     * @param DestinationInterface    $destination
     * @param InteropMessage $message
     */
    public function __construct(DestinationInterface $destination, InteropMessage $message)
    {
        $this->destination = $destination;
        $this->message = $message;
    }

    /**
     * @return DestinationInterface
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return InteropMessage
     */
    public function getMessage()
    {
        return $this->message;
    }
}
