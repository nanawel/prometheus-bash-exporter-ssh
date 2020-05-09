<?php

function extractMeminfo(array $meminfo) {
    $results = [];
    foreach($meminfo as $line) {
        if (!$line) {
            continue;
        }
        list($label, $value) = array_slice(preg_split('/\s+/', $line), 0, 2);
        $results[trim($label, ':')] = (int) $value;
    }

    return $results;
}

(new Probe())->sendResults(
    extractMeminfo(explode("\n", file_get_contents('/proc/meminfo')))
);