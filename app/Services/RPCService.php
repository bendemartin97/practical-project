<?php

namespace App\Services;


use App\Models\Result;
use Spiral\Goridge\RPC\RPC;
use Spiral\Goridge\SocketRelay;

class RPCService
{

    public const SOCK_PORT = 6001;
    public const SOCK_TYPE = SocketRelay::SOCK_TCP;

    const TYPE = "rpc";

    private RPC $rpc;
    public function __construct()
    {
        $this->rpc = $this->makeRPC();
    }

    public function calculate(): Result
    {
        $result = $this->rpc->call("App.Calculate", null);
        return Result::fromJsonString($result, self::TYPE);
    }
    /**
     * @return RPC
     */
    private function makeRPC(): RPC
    {
        return new RPC($this->makeRelay());
    }

    /**
     * @return SocketRelay
     */
    private function makeRelay(): SocketRelay
    {
        $addr = config('app.go_rpc_container');
        return new SocketRelay($addr, static::SOCK_PORT, static::SOCK_TYPE);
    }
}
