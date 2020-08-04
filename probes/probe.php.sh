#!/bin/bash

source "$(dirname "$(readlink -f ${BASH_SOURCE[0]})")/probe.env.sh"
probeBaseDir="$rootDir/probes/php"
probeScript="$probeBaseDir/${probe}.php"

# Prepare standalone archive
tmpDir=$(mktemp -d)
trap "{ rm -r $tmpDir; }" EXIT
cat $probeBaseDir/probe.php $probeScript > $tmpDir/script.php
cp $probeConf $tmpDir/script.conf

# Run!
tar -czOC "$tmpDir" script.php script.conf \
    | ssh -C \
        -o LogLevel=ERROR \
        -o StrictHostKeyChecking=${SSH_HOST_KEY_CHECKING:-no} \
        -o ConnectTimeout=${SSH_CONNECT_TIMEOUT:-5} \
        ${SSH_USER}@${SSH_HOST} -p${SSH_PORT:-22}${SSH_ARGS} "sh -c '\
            ${AFTER_CONNECT_SCRIPT} \
            export PROBE_HOSTNAME=${SSH_HOST} \
                   PROBE_ENV=${env} \
                   PROBE_NAME=${probe} \
                   tmp=\$(mktemp -d); \
            tar -xzC \$tmp; php -f \$tmp/script.php < \$tmp/script.conf; rm -r \$tmp;'"

# Force exit 0 to prevent container from exiting in case probe experienced a temporary failure
exit 0
