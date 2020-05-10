import os
import re
import subprocess

class DiskFree(Probe):
    def run(self):
        p = subprocess.Popen(["df"], stdout=subprocess.PIPE, env={'LANG': 'C'})
        output = p.communicate()[0].decode('utf-8').splitlines()

        results = {}
        for l in output:
            values = re.split('\s+', l)
            if values[0].startswith('/dev/'):
                results[values[0]] = int(values[4].rstrip('%'))
        
        self.sendResults(sorted(results))

DiskFree().run()
