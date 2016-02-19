<?php

use React\Socket\Connection;

require __DIR__.'/../vendor/autoload.php';

$host = '127.0.0.1';
$port = 4000;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$clients = new \SplObjectStorage();

$socket->on('connection', function (Connection $client) use ($clients) {

    $clients->attach($client);
    printf("Client connected: %s (new total: %d)\n", $client->getRemoteAddress(), count($clients));

    $client->on('data', function ($data) use ($clients, $client) {
        printf("[%s] Server received: %s\n", date('H:i:s'), $data);

        foreach ($clients as $current) {
            if ($client === $current) {
                continue;
            }

            $current->write($client->getRemoteAddress().': ');
            $current->write($data);
        }
    });

    $client->on('end', function () use ($clients, $client) {
        $clients->detach($client);
        printf("Client leaved: %s (new total: %d)\n", $client->getRemoteAddress(), count($clients));
    });
});

echo "Socket server listening on port $port.\n";
echo "You can connect to it by running: telnet $host $port\n";

$socket->listen($port, $host);
$loop->run();
