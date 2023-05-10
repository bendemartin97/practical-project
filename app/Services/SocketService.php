<?php

namespace App\Services;

use App\Models\Result;
use WebSocket\Client;

class SocketService implements ConnectionInterface
{

    private string $address;
    private Client $client;

    const TYPE = "socket";
    public function __construct()
    {
        $this->address = config('app.go_websocket_container');
        $this->client = new Client("ws://$this->address:6002");
    }

    /**
     * @return void
     */
    private function send(): void
    {
        $this->client->text("calculate");
    }

    /**
     * @return Result
     */
    private function receive(): Result
    {
        $result = $this->client->receive();
        $this->client->close();
        return Result::fromJsonString($result, self::TYPE);
    }

    public function calculate(): Result
    {
        $this->send();
        return $this->receive();
    }
}
