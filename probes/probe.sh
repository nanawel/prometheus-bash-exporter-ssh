#!/bin/bash

rootDir="$(readlink -f "$(dirname "$0")/..")"

calledScript="$(basename -s '.sh' "$0")"
env="$(echo "$calledScript" | cut -d_ -f1)"
probe="$(echo "$calledScript" | cut -d_ -f2)"
probeWithArgs="$(echo "$calledScript" | cut -d_ -f2-)"

envFile="$rootDir/conf/${env}.env"
probeScript=$([ -f "$rootDir/probes/${probe}.php" ] && echo "$rootDir/probes/${probe}.php" || echo "")
probeConf=$([ -f "$rootDir/conf/${env}_${probeWithArgs}.conf" ] && echo "$rootDir/conf/${env}_${probeWithArgs}.conf" || echo "/dev/null")

[ -r "$envFile" ] && source "$envFile" || { echo "Missing env file '$envFile'."; exit 1; }

# Prepare standalone archive
tmpDir=$(mktemp -d)
trap "{ rm -r $tmpDir; }" EXIT
cat $rootDir/probes/probe.php $probeScript > $tmpDir/script.php
cp $probeConf $tmpDir/script.conf

# Run!
tar -czOC "$tmpDir" script.php script.conf \
    | ssh -C \
        -o LogLevel=ERROR \
        -o StrictHostKeyChecking=${SSH_HOST_KEY_CHECKING:-no} \
        -o ConnectTimeout=${SSH_CONNECT_TIMEOUT:-5} \
        ${SSH_USER}@${SSH_HOST} -p${SSH_PORT:-22} "sh -c '\
            export PROBE_HOSTNAME=${SSH_HOST} \
                   PROBE_ENV=${env} \
                   PROBE_NAME=${probe} \
                   tmp=\$(mktemp -d); \
            tar -xzC \$tmp; php -f \$tmp/script.php < \$tmp/script.conf; rm -r \$tmp;'"

# Force exit 0 to prevent container from exiting in case probe experienced a temporary failure
exit 0
