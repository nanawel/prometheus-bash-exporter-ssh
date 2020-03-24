#!/bin/bash

set -e
rootDir="$(readlink -f "$(dirname "$0")/..")"

calledScript="$(basename -s '.sh' "$0")"
env="$(echo "$calledScript" | cut -d_ -f1)"
probe="$(echo "$calledScript" | cut -d_ -f2)"

envFile="$rootDir/conf/${env}.env"
probeScript=$([ -f "$rootDir/probes/${probe}.php" ] && echo "$rootDir/probes/${probe}.php" || echo "")
# Not used atm
probeConf=$([ -f "$rootDir/conf/${env}_${probe}.conf" ] && echo "$rootDir/conf/${env}_${probe}.conf" || echo "")

[ -r "$envFile" ] && source "$envFile" || { echo "Missing env file."; exit 1; }

# Run!
cat ${rootDir}/probes/probe.php $probeScript \
    | ssh -C \
        -o StrictHostKeyChecking=no \
        -o LogLevel=ERROR \
        ${SSH_USER}@${SSH_HOST} -p${SSH_PORT:-22} PROBE_HOSTNAME="${SSH_HOST}" PROBE_ENV="${env}" PROBE_NAME=${probe} php -f /dev/stdin

# Force exit 0 to prevent container from exiting in case probe experienced a temporary failure
exit 0