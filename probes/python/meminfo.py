
class MemInfo(Probe):
    def run(self):
        with open('/proc/meminfo') as f:
            rawMemInfo = f.read()
        rawMemInfo = rawMemInfo.splitlines()
        memInfo = {}
        for l in rawMemInfo:
            label, value = l.split(':')
            memInfo[label] = value.strip().split(' ')[0]
        self.sendResults(memInfo)

MemInfo().run()
