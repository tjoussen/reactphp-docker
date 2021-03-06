<?php

// this example shows how the `containerStatsStream()` method can be used show live container stats.
// demonstrates the JSON streaming API, individual stats events will be printed as they happen.

use Clue\React\Docker\Client;

require __DIR__ . '/../vendor/autoload.php';

$container = isset($argv[1]) ? $argv[1] : 'asd';
echo 'Monitoring "' . $container . '" (pass as argument to this example)' . PHP_EOL;

$loop = React\EventLoop\Factory::create();
$client = new Client($loop);

$stream = $client->containerStatsStream($container);

$stream->on('data', function ($progress) {
    //echo 'progress: '. json_encode($progress, JSON_PRETTY_PRINT) . PHP_EOL;

    $memory = $progress['memory_stats']['usage'] / $progress['memory_stats']['limit'] * 100;
    $sent = $received = 0;
    foreach ($progress['networks'] as $stats) {
        $sent += $stats['tx_bytes'];
        $received += $stats['rx_bytes'];
    }

    echo round($memory, 3) . '% memory and ' . $received . '/' . $sent . ' bytes network I/O' . PHP_EOL;
});
$stream->on('error', 'printf');
$stream->on('close', function () {
    echo 'stream closed' . PHP_EOL;
});

$loop->run();
