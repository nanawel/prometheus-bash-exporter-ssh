import os
import subprocess

class LoadAvg(Probe):
    def run(self):
        load1, load5, load15 = os.getloadavg()
        cpuCount = int(subprocess.check_output(['nproc']))
        self.sendResults({'1m': load1, '5m': load5, '15m': load15, 'nproc': cpuCount})

LoadAvg().run()
