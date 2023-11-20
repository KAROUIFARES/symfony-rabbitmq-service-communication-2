<?php

namespace App\MessageHandler;

use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
class SendMessageToProductService
{
    //private OutputInterface $output;
    public function execute(String $id)
    {
        $connection = new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            $_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_USER'],
            $_ENV['RABBITMQ_PASSWORD']
        );
        $channel = $connection->channel();
        $channel->queue_declare('data_queue', false, true, false, false);
        $message = new AMQPMessage($id);
        $channel->basic_publish($message, '', 'data_queue');
        //$this->output->writeln('Data sent to RabbitMQ');
        $channel->close();
        $connection->close();
    }
}