<?php

namespace App\Services;

use App\Models\Result;
use Exception;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqService implements ConnectionInterface
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    private const PUBLISH_QUEUE_NAME = 'php-to-go';
    private const RECEIVE_QUEUE_NAME = 'go-to-php';

    private ?int $currentTicket = null;

    private string $message = '';

    const TYPE = "rabbitmq";

    /**
     * @throws Exception
     */
    public function __construct(
        public bool $passive = false,
        public bool $durable = false,
        public bool $exclusive = false,
        public bool $auto_delete = false,

    )
    {
        $host = config('app.rabbitmq.host');
        $port = config('app.rabbitmq.port');
        $user = config('app.rabbitmq.user');
        $password = config('app.rabbitmq.password');

        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();

        $this->initQueues();
    }

    /**
     * @return void
     */
    private function initQueues(): void
    {
        $this->channel->queue_declare(self::PUBLISH_QUEUE_NAME, $this->passive, $this->durable, $this->exclusive, $this->auto_delete);
        $this->channel->queue_declare(self::RECEIVE_QUEUE_NAME, $this->passive, $this->durable, $this->exclusive, $this->auto_delete);
    }

    /**
     * @param $message
     * @return void
     */
    private function publish($message): void
    {
        if ($this->currentTicket) {
            Log::info('Waiting for the previous message to be processed');
            return;
        }

        $this->currentTicket = rand(1000, 9999);

        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, '', self::PUBLISH_QUEUE_NAME, ticket: $this->currentTicket);
    }

    /**
     */
    private function receive(): Result
    {
        $this->channel->basic_consume(
            self::RECEIVE_QUEUE_NAME,        #queue
            'receive',                     #consumer tag - Identifier for the consumer, valid within the current channel. just string
            false,                  #no local - TRUE: the server will not send messages to the connection that published them
            false,                  #no ack, false - acks turned on, true - off.  send a proper acknowledgment from the worker, once we're done with a task
            false,                  #exclusive - queues may only be accessed by the current connection
            false,                  #no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
            function (AMQPMessage $msg) {
                $this->message = $msg->body;
                $msg->ack();
                $this->channel->basic_cancel('receive');
            }
        );

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }


        return Result::fromJsonString($this->message, self::TYPE);
    }

    public function calculate(): Result
    {
        $this->publish("calculate");
        return $this->receive();
    }
}
