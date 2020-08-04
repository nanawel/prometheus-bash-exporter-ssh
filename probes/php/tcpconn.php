<?php

class TcpConn extends Probe {
    // TODO Implement support
    const DEFAULT_TIMEOUT = 2.0;

    const METHOD_NATIVE = 'native';
    const METHOD_NETCAT = 'netcat';

    public function run()
    {
        $config = json_decode($this->getConfig(), true);
        
        $results = array();
        
        $startTime = microtime(true);
        $hostIp = filter_var($config['host'], FILTER_VALIDATE_IP)
            ? $config['host']
            : gethostbyname($config['host']);
        $results['resolve_time'] = microtime(true) - $startTime;
        switch($config['connect_method'] ?? self::METHOD_NATIVE) {
            case self::METHOD_NATIVE:
                if (function_exists('socket_create')) {
                    $results = array_merge($results, $this->runNative($config, $hostIp));
                    break;
                }
                // No break, fallback to netcat

            case self::METHOD_NETCAT:
                $results = array_merge($results, $this->runNetcat($config, $hostIp));
        }

        $this->sendResults(
            $results + [
                // Default values
                'connect_time' => 0,
            ],
            [
                'probe_args' => sprintf('%s:%d', $config['host'], $config['port'])
            ]
        );
    }

    protected function runNative($config, $hostIp) {
        $results = array();
        
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
            $results['banner_read_time'] = strpos($banner, $config['banner']) !== false
                ? microtime(true) - $startTime
                : 0;
        }
        
        socket_close($socket);

        return $results;
    }

    protected function runNetcat($config, $hostIp) {
        $results = array();

        $startTime = microtime(true);
        $out = system(sprintf('nc -n -z %s %d', $hostIp, $config['port']), $rc);
        if (false === $out || $rc  !== 0) {
            return [];
        }
        $results['connect_time'] = microtime(true) - $startTime;
        
        if (!empty($config['banner'])) {
            // Not supported
            $results['banner_read_time'] = 0;
        }

        return $results;
    }
}

$probe = new TcpConn();
$probe->run();