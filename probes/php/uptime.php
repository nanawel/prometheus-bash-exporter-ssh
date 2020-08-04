<?php

$uptime = preg_split('/\s+/', file_get_contents('/proc/uptime'));

$probe = new Probe();
$probe->sendResults(array(
    'uptime' => (float) $uptime[0],
    'uptime_idle' => (float) $uptime[1],
));