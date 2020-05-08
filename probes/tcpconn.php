<?php

$probe = new Probe();
$config = json_decode($probe->getConfig(), true);

$results = [];

$startTime = microtime(true);
if (!filter_var($config['host'], FILTER_VALIDATE_IP)) {
    $config['host'] = gethostbyname($config['host']);
}
$results['resolve_time'] = microtime(true) - $startTime;

if (false === ($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
    $results['error'] = true;
}
$startTime = microtime(true);
if (false === (socket_connect($socket, $config['host'], $config['port']))) {
    $results['error'] = true;
}
$results['connect_time'] = microtime(true) - $startTime;

if (!empty($config['banner'])) {
    $banner = socket_read($socket, 255);
    $results['banner_found'] = strpos($banner, $config['banner']) === 0;
}

socket_close($socket);

$probe->sendResults(
    $results,
    [
        'service' => sprintf('%s:%d', $config['host'], $config['port'])
    ]
);