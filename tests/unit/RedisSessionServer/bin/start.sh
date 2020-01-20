#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

nohup /usr/bin/env php "$__DIR__/imi" server/start -name main > "$__DIR__/../logs/cli.log" 2>&1 &
