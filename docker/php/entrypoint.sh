#!/bin/sh

export CONFD_ARGS="${CONFD_ARGS:--onetime -backend env}"
export DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)
if [ "$1" = 'php-fpm' ]; then
    /bin/confd ${CONFD_ARGS}
    php-fpm
else
    /bin/confd ${CONFD_ARGS}
    exec /usr/bin/env "$@"
fi