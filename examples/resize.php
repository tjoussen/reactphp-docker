<?php

// this example tries to adjust the TTY size of the given container to 10x10.
// you can check this via "docker logs".

use Clue\React\Docker\Client;

require __DIR__ . '/../vendor/autoload.php';

$container = isset($argv[1]) ? $argv[1] : 'asd';

$loop = React\EventLoop\Factory::create();
$client = new Client($loop);

$client->containerInspect($container)->then(function ($info) use ($client, $container) {
    $size = $info['HostConfig']['ConsoleSize'];

    echo 'Current TTY size is ' . $size[0] . 'x' . $size[1] . PHP_EOL;

    return $client->containerResize($container, $size[0] + 10, $size[1] + 10);
})->then(function () use ($client) {
    echo 'Successfully set' . PHP_EOL;
}, 'printf');


$loop->run();
