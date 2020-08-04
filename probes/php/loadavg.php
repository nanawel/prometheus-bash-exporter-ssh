<?php

$load = array_slice(preg_split('/\s+/', file_get_contents('/proc/loadavg')), 0, 3);
$nproc = exec('nproc');

$probe = new Probe();
$probe->sendResults(array(
    '1m' => (float) $load[0],
    '5m' => (float) $load[1],
    '15m' => (float) $load[2],
    'nproc' => (int) $nproc
));