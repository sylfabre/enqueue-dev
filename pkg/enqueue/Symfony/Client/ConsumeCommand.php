<?php

namespace Enqueue\Symfony\Client;

use Enqueue\Client\DriverInterface;
use Enqueue\Consumption\ChainExtension;
use Enqueue\Consumption\Extension\LoggerExtension;
use Enqueue\Consumption\ExtensionInterface;
use Enqueue\Consumption\QueueConsumerInterface;
use Enqueue\Symfony\Consumption\ChooseLoggerCommandTrait;
use Enqueue\Symfony\Consumption\LimitsExtensionsCommandTrait;
use Enqueue\Symfony\Consumption\QueueConsumerOptionsCommandTrait;
use Interop\Queue\ProcessorInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeCommand extends Command
{
    use LimitsExtensionsCommandTrait;
    use SetupBrokerExtensionCommandTrait;
    use QueueConsumerOptionsCommandTrait;
    use ChooseLoggerCommandTrait;

    protected static $defaultName = 'enqueue:consume';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $queueConsumerIdPattern;

    /**
     * @var string
     */
    private $driverIdPattern;

    /**
     * @var string
     */
    private $processorIdPattern;

    public function __construct(
        ContainerInterface $container,
        string $queueConsumerIdPattern = 'enqueue.client.%s.queue_consumer',
        string $driverIdPattern = 'enqueue.client.%s.driver',
        string $processorIdPatter = 'enqueue.client.%s.delegate_processor'
    ) {
        parent::__construct(self::$defaultName);

        $this->container = $container;
        $this->queueConsumerIdPattern = $queueConsumerIdPattern;
        $this->driverIdPattern = $driverIdPattern;
        $this->processorIdPattern = $processorIdPatter;
    }

    protected function configure(): void
    {
        $this->configureLimitsExtensions();
        $this->configureSetupBrokerExtension();
        $this->configureQueueConsumerOptions();
        $this->configureLoggerExtension();

        $this
            ->setAliases(['enq:c'])
            ->setDescription('A client\'s worker that processes messages. '.
                'By default it connects to default queue. '.
                'It select an appropriate message processor based on a message headers')
            ->addArgument('client-queue-names', InputArgument::IS_ARRAY, 'Queues to consume messages from')
            ->addOption('skip', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Queues to skip consumption of messages from', [])
            ->addOption('client', 'c', InputOption::VALUE_OPTIONAL, 'The client to consume messages from.', 'default')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $client = $input->getOption('client');

        try {
            $consumer = $this->getQueueConsumer($client);
        } catch (NotFoundExceptionInterface $e) {
            throw new \LogicException(sprintf('Client "%s" is not supported.', $client), null, $e);
        }

        $driver = $this->getDriver($client);
        $processor = $this->getProcessor($client);

        $this->setQueueConsumerOptions($consumer, $input);

        $clientQueueNames = $input->getArgument('client-queue-names');
        if (empty($clientQueueNames)) {
            $clientQueueNames[$driver->getConfig()->getDefaultQueue()] = true;
            $clientQueueNames[$driver->getConfig()->getRouterQueue()] = true;

            foreach ($driver->getRouteCollection()->all() as $route) {
                if ($route->getQueue()) {
                    $clientQueueNames[$route->getQueue()] = true;
                }
            }

            foreach ($input->getOption('skip') as $skipClientQueueName) {
                unset($clientQueueNames[$skipClientQueueName]);
            }

            $clientQueueNames = array_keys($clientQueueNames);
        }

        foreach ($clientQueueNames as $clientQueueName) {
            $queue = $driver->createQueue($clientQueueName);
            $consumer->bind($queue, $processor);
        }

        $consumer->consume($this->getRuntimeExtensions($input, $output));

        return null;
    }

    protected function getRuntimeExtensions(InputInterface $input, OutputInterface $output): ExtensionInterface
    {
        $extensions = [new LoggerExtension(new ConsoleLogger($output))];
        $extensions = array_merge($extensions, $this->getLimitsExtensions($input, $output));

        $driver = $this->getDriver($input->getOption('client'));

        if ($setupBrokerExtension = $this->getSetupBrokerExtension($input, $driver)) {
            $extensions[] = $setupBrokerExtension;
        }

        if ($loggerExtension = $this->getLoggerExtension($input, $output)) {
            array_unshift($extensions, $loggerExtension);
        }

        return new ChainExtension($extensions);
    }

    private function getDriver(string $name): DriverInterface
    {
        return $this->container->get(sprintf($this->driverIdPattern, $name));
    }

    private function getQueueConsumer(string $name): QueueConsumerInterface
    {
        return $this->container->get(sprintf($this->queueConsumerIdPattern, $name));
    }

    private function getProcessor(string $name): ProcessorInterface
    {
        return $this->container->get(sprintf($this->processorIdPattern, $name));
    }
}
