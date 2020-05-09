# coding: utf-8

import fileinput
import json
import os
import sys

class Probe:
    def __init__(self): # Notre méthode constructeur
        self.host = os.environ['PROBE_HOSTNAME']
        self.env = os.environ['PROBE_ENV']
        self.probe = os.environ['PROBE_NAME']
    
    def getConfig(self):
        return sys.stdin.read()
    
    def sendResults(self, results, additionalLabels = {}):
        labels = {'hostname': self.host, 'env': self.env, 'probe': self.probe, 'probe_args': ''}
        labels.update(additionalLabels)
        print(json.dumps({'labels': labels, 'results': results}))

