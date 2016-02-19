<?php

$host = '127.0.0.1';
$port = 4000;

$server = @stream_socket_server("tcp://$host:$port", $errno, $errstr);
if (false === $server) {
    $message = "Could not bind to tcp://$host:$port: $errstr";
    throw new \RuntimeException($message, $errno);
}
stream_set_blocking($server, 0);

echo "Socket server listening on port $port.\n";
echo "You can connect to it by running: telnet $host $port\n";

$clients = [];

while (true) {
    $reads = array_merge($clients, [$server]);
    $writes = $excepts = [];

    if (stream_select($reads, $writes, $excepts, 1, 0) === false) {
        echo "Timeout !\n";
    }

    foreach ($reads as $read) {
        if ($read === $server) {
            if ($client = stream_socket_accept($server)) {
                $clients[] = $client;
                printf("Client connected: %s (new total: %d)\n", stream_socket_get_name($client, false), count($clients));
            }
        } elseif (($data = trim(fread($read, 1024))) !== '') {
            printf("[%s] Server received: %s\n", date('H:i:s'), $data);

            foreach ($clients as $current) {
                if ($read === $current) {
                    continue;
                }

                fprintf($current, "%s: %s\n", stream_socket_get_name($read, false) ,$data);
            }
        }
    }
}

fclose($server);
