export const connections = [
    {
        key: 'php',
        name: 'Test through PHP',
        info: 'The calculating will be made in PHP self, so it could take a while, given the specificities of the programming language',
        route: 'php',
        img: 'php.png',
        fullWidth: false
    },
    {
        key: 'rpc',
        name: 'Test through RPC',
        info: 'In distributed computing, a remote procedure call (RPC) is when a computer program causes a procedure (subroutine) to execute in a different address space (commonly on another computer on a shared network), which is written as if it were a normal (local) procedure call. ' +
            'With this possibility, the PHP server calls the program written and launched in GO, which will do the calculation',
        route: 'rpc',
        img: 'rpc.png',
        fullWidth: false
    },
    {
        key: 'rabbitmq',
        name: 'Test through RabbitMQ',
        info: 'RabbitMQ is an open-source message-broker software that originally implemented the Advanced Message Queuing Protocol and has since been extended with a plug-in architecture to support Streaming Text Oriented Messaging Protocol, Message Queuing Telemetry Transport, and other protocols.' +
            'After the request, a message will be pushed on a queue, where a server written in GO is listening. PHP server is listening on an another queue and blocking, until the answer is not on the receive queue. When the answer is received, the PHP server will send it to the client.',
        route: 'rabbitmq',
        img: 'rabbitmq.png',
        fullWidth: true
    },
    {
        key: 'ffi',
        name: 'Test through FFI',
        info: 'FFI is a PHP extension that allows to call directly C functions and to access C data structures and primitive types from PHP. The program written in GO could be cross-compiled in a shared library, which will be loaded by the PHP server. ' +
            'The PHP server will call the functions from the shared library. According to php.net, FFI is about 2 times slower than accessing native PHP arrays and objects. Thanks to the multithreading in GO, the calculation is nevertheless fast.',
        route: 'ffi',
        img: 'ffi.png',
        fullWidth: false
    },
    {
        key: 'websocket',
        name: 'Test through WebSocket',
        info: 'WebSocket is a computer communications protocol, providing full-duplex communication channels over a single TCP connection. A websocket server is launched in GO, which is waiting for a request. When the request is received, ' +
            'the server will send the answer to the PHP server. Thanks to the package texttalk/websocket we can very simple handle two-ways messaging.',
        route: 'websocket',
        img: 'websocket.png',
        fullWidth: false
    },
]

export type Connection = {
    key: string,
    name: string,
    info: string,
    route: string,
}

export const ROUTES = [
    'rpc',
    'php',
    'websocket',
    'rabbitmq',
    'ffi',
]
