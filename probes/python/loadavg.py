import os
import subprocess

class LoadAvg(Probe):
    def run(self):
        load1, load5, load15 = os.getloadavg()
        # Implementation that should maximize compatibility (even with Python 2.5)
        p = subprocess.Popen(['nproc'], stdout=subprocess.PIPE)
        p.wait()
        cpuCount = int(p.communicate()[0])
        self.sendResults({'1m': load1, '5m': load5, '15m': load15, 'nproc': cpuCount})

LoadAvg().run()
