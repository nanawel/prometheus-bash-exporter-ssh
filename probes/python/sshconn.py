import json
import os
import subprocess
import time

class SshConn(Probe):
    DEFAULT_TIMEOUT=3.0

    def run(self):
        config = json.loads(self.getConfig())
        if not 'options' in config:
            config['options'] = ''
        if not 'port' in config:
            config['port'] = 22
        if not 'timeout' in config:
            config['timeout'] = self.DEFAULT_TIMEOUT
        
        results = {}
        try:
            startTime = self.perfCounter()

            devNull = open('/dev/null')

            cmd = ['ssh']
            cmd.append('-tt')
            cmd.extend(config['options'])
            cmd.extend(['{0}@{1}'.format(config['user'], config['host']), '-p', str(config['port'])])
            
            p = subprocess.Popen(cmd, stdin=subprocess.PIPE, stdout=devNull, stderr=devNull)
            p.communicate(input=b"exit\n", timeout=config['timeout'])
            if p.returncode != 0:
                raise Exception('Unexpected return code: ' + p.returncode)
            
            results['connect_time'] = self.perfCounter() - startTime
        except Exception as e:
            #print('ERROR: ', e)
            results.update({'connect_time': 0})
    
        self.sendResults(results, {'probe_args': '{0}@{1}:{2}'.format(config['user'], config['host'], config['port'])})
    
    def perfCounter(self):
        try:
            return time.perf_counter()
        except:
            return time.clock()

SshConn().run()
