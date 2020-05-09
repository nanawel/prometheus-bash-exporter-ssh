
class MemInfo(Probe):
    def run(self):
        rawMemInfo = open('/proc/meminfo').read().splitlines()
        memInfo = {}
        for l in rawMemInfo:
            label, value = l.split(':')
            memInfo[label] = float(value.strip().split(' ')[0])
        self.sendResults(memInfo)

MemInfo().run()
