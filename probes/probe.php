<?php

//** @see https://stackoverflow.com/a/43056278 */
ini_set('serialize_precision', -1);

class Probe
{
    /**@var string */
    protected
        $host,
        $env,
        $probe;

    public function __construct() {
        $this->host = getenv('PROBE_HOSTNAME');
        $this->env = getenv('PROBE_ENV');
        $this->probe = getenv('PROBE_NAME');
    }

    public function sendResults($results) {
        echo json_encode([
            'labels' => [
                'hostname' => $this->host,
                'env' => $this->env,
                'probe' => $this->probe
            ],
            'results' => $results
        ]);
    }
}
?>