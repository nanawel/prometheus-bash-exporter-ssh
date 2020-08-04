<?php

class Mdstate extends Probe {
    public function run()
    {
        $config = json_decode($this->getConfig(), true);

        $deviceId = str_replace('/dev/', '', $config['device']);
        $results = array(
            'degraded' => (int) file_get_contents("/sys/block/$deviceId/md/degraded"),
            'raid_disks' => (int) file_get_contents("/sys/block/$deviceId/md/raid_disks"),
            'active' => (int) file_get_contents("/sys/block/$deviceId/md/active"),
        );
        $this->sendResults($results, array('probe_args' => "device:{$config['device']}",));
    }
}

$probe = new Mdstate();
$probe->run();