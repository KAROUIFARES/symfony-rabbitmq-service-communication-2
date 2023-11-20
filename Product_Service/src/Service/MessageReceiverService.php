<?php
namespace App\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ReceiveFromService1Command extends Command
{
    protected static $defaultName = 'app:receive-from-service1';

    protected function configure()
    {
        $this
            ->setDescription('Receives a message from Service1 via RabbitMQ')
            ->setHelp('This command receives a message from Service1 via RabbitMQ and stores it in a variable.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            $_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_USER'],
            $_ENV['RABBITMQ_PASSWORD']
        );
        $channel = $connection->channel();

        $channel->queue_declare('data_queue', false, true, false, false);

        $output->writeln('Waiting for a message from Service1...');

        $callback = function ($message) use ($output) {
            $output->writeln('Message received from Service1: ' . $message->body);
            $receivedMessage = $message->body;
        };
        $channel->basic_consume('data_queue', '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}