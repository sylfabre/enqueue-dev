<?php

namespace Enqueue\Client;

use Enqueue\Consumption\Result;
use Interop\Queue\ContextInterface;
use Interop\Queue\MessageInterface as InteropMessage;
use Interop\Queue\ProcessorInterface;

final class RouterProcessor implements ProcessorInterface
{
    /**
     * @var DriverInterface
     */
    private $driver;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function process(InteropMessage $message, ContextInterface $context): Result
    {
        if ($message->getProperty(Config::COMMAND)) {
            return Result::reject(sprintf(
                'Unexpected command "%s" got. Command must not go to the router.',
                $message->getProperty(Config::COMMAND)
            ));
        }

        $topic = $message->getProperty(Config::TOPIC);
        if (false == $topic) {
            return Result::reject(sprintf('Topic property "%s" is required but not set or empty.', Config::TOPIC));
        }

        $count = 0;
        foreach ($this->driver->getRouteCollection()->topic($topic) as $route) {
            $clientMessage = $this->driver->createClientMessage($message);
            $clientMessage->setProperty(Config::PROCESSOR, $route->getProcessor());

            $this->driver->sendToProcessor($clientMessage);

            ++$count;
        }

        return Result::ack(sprintf('Routed to "%d" event subscribers', $count));
    }
}
