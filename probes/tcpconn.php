<?php

$probe = new Probe();
$config = json_decode($probe->getConfig(), true);

$results = [];

$startTime = microtime(true);
$hostIp = filter_var($config['host'], FILTER_VALIDATE_IP)
    ? $config['host']
    : gethostbyname($config['host']);
$results['resolve_time'] = microtime(true) - $startTime;

if (false === ($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
    exit(1);
}
$startTime = microtime(true);
if (false === (socket_connect($socket, $hostIp, $config['port']))) {
    exit(1);
}
$results['connect_time'] = microtime(true) - $startTime;

if (!empty($config['banner'])) {
    $startTime = microtime(true);
    $banner = socket_read($socket, 255);
    $results['banner_read_time'] = strpos($banner, $config['banner']) === 0
        ? microtime(true) - $startTime
        : -1;
}

socket_close($socket);

$probe->sendResults(
    $results,
    [
        'probe_args' => sprintf('%s:%d', $config['host'], $config['port'])
    ]
);