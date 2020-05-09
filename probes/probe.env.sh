#!/bin/bash

rootDir="$(readlink -f "$(dirname "$0")/..")"

calledScript="$(basename -s '.sh' "$0")"
env="$(echo "$calledScript" | cut -d_ -f1)"
probe="$(echo "$calledScript" | cut -d_ -f2 | cut -d. -f1)"
probeWithArgs="$(echo "$calledScript" | cut -d_ -f2- | cut -d. -f1)"

envFile="$rootDir/conf/${env}.env"
probeConf=$([ -f "$rootDir/conf/${env}_${probeWithArgs}.conf" ] && echo "$rootDir/conf/${env}_${probeWithArgs}.conf" \
    || echo "/dev/null")

[ -r "$envFile" ] && source "$envFile" || { echo "Missing env file '$envFile'."; exit 1; }
