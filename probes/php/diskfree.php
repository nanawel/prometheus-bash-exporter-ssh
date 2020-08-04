<?php

exec('df', $out);
$df = array_filter(
    array_map(function($line) {
        return preg_split('/\s+/', $line);
    }, $out),
    function($item) {
        return strpos($item[0], '/dev/') === 0;
    }
);
usort($df, function($a, $b) {
    return strcmp($a[0], $b[0]);
});

$results = array();
foreach ($df as $line) {
    $results[$line[0]] = (int) $line[4];
}

$probe = new Probe();
$probe->sendResults($results);