<?php

use Symfony\Component\HttpFoundation\Request;

$port = 8080;
$host = '127.0.0.1';

$loader = require __DIR__.'/autoload.php';

$kernel = new AppKernel('prod', true);
$kernel->loadClassCache();
$kernel->boot();

$loop = new React\EventLoop\StreamSelectLoop();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket, $loop);

$i = 0;

$http->on('request', function (React\Http\Request $reactRequest, React\Http\Response $reactResponse) use (&$i, $kernel, $port, $host) {
    $i++;

    $kernel->boot();

    $server = array(
        'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'],
        'REMOTE_ADDR' => $reactRequest->remoteAddress,
        'SERVER_PROTOCOL' => 'HTTP'.$reactRequest->getHttpVersion(),
        'SERVER_NAME' => $host,
        'SERVER_PORT' => $port,
        'REQUEST_URI' => $reactRequest->getPath(),
        'REQUEST_METHOD' => $reactRequest->getMethod(),
        'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],
        'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'],
        'PHP_SELF' => $reactRequest->getPath(),
    );
    foreach ($reactRequest->getHeaders() as $key => $value) {
        $key = 'HTTP_'.str_replace('-', '_', strtoupper($key));
        $server[$key] = $value;
    }

    $sfRequest = new Request($reactRequest->getQuery(), array(), array(), array(), array(), $server);

    $sfResponse = $kernel->handle($sfRequest);
    $headers = [];
    foreach ($sfResponse->headers->allPreserveCase() as $name => $values) {
        foreach ($values as $value) {
            $headers[$name] = $value;
        }
    }

    $reactResponse->writeHead($sfResponse->getStatusCode(), $headers);
    $reactResponse->end($sfResponse->getContent());
    $kernel->terminate($sfRequest, $sfResponse);
});

$loop->addPeriodicTimer(2, function () use (&$i) {
    $kmem = memory_get_usage(true) / 1024;
    echo "Request: $i\n";
    echo "Memory: $kmem KiB\n";
});

$socket->listen($port, $host);
$loop->run();
