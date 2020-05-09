import json
import os
import socket
import time

class TcpConn(Probe):
    def run(self):
        config = json.loads(self.getConfig())
        
        startTime = time.perf_counter()
        hostIp = socket.gethostbyname(config['host'])
        results = {'resolve_time': time.perf_counter() - startTime}

        try:
            startTime = time.perf_counter()
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.connect((hostIp, config['port']))
            results['connect_time'] = time.perf_counter() - startTime

            try:
                if 'banner' in config:
                    startTime = time.perf_counter()
                    banner = sock.recv(255).decode('utf-8')
                    results['banner_read_time'] = time.perf_counter() - startTime
                    if banner.find(config['banner']) == -1:
                        results['banner_read_time'] = -1
            except Exception as e:
                #print(e)
                results.update({'banner_read_time': -1})

            sock.close()
        except Exception as e:
            #print(e)
            results.update({'connect_time': -1, 'banner_read_time': -1})
    
        results.update({'probe_args': '{0}:{1}'.format(config['host'], config['port'])})
        self.sendResults(results)

TcpConn().run()
