import os
import subprocess

class LoadAvg(Probe):
    def run(self):
        load1, load5, load15 = os.getloadavg()
        
        # Python 2/3 compatibility implementation
        rawCpuInfo = open('/proc/cpuinfo').read().splitlines()
        cpuCount = 0
        for line in rawCpuInfo:
            if line.startswith("processor\t:"):
                cpuCount += 1

        self.sendResults({'1m': load1, '5m': load5, '15m': load15, 'nproc': cpuCount})

LoadAvg().run()
