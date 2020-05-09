import os

class LoadAvg(Probe):
    def run(self):
        load1, load5, load15 = os.getloadavg()
        cpuCount = os.cpu_count() if os.cpu_count() else -1
        self.sendResults({'1m': load1, '5m': load5, '15m': load15, 'nproc': cpuCount})

LoadAvg().run()
