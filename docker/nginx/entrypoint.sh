#!/usr/bin/env sh

substitute_environment_variables()
{
    envsubst $(printenv | cut -f1 -d'=' | sed 's/.*/\\\${&}/' | tr '\n' ',')
}

set -e
substitute_environment_variables < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf
nginx -g 'daemon off;'

exit 0