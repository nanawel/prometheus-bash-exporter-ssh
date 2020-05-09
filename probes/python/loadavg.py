import os
import subprocess

class LoadAvg(Probe):
    def run(self):
        load1, load5, load15 = os.getloadavg()
        
        # Compatibility implementation
        with open('/proc/cpuinfo') as f:
            rawCpuInfo = f.read()
        rawCpuInfo = rawCpuInfo.splitlines()

        cpuCount = 0
        for line in rawCpuInfo:
            print(line)
            if line.startswith("processor\t"):
                cpuCount += 1

        self.sendResults({'1m': load1, '5m': load5, '15m': load15, 'nproc': cpuCount})

LoadAvg().run()
