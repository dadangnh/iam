#!/usr/bin/env sh
set -eu

# As of version 1.19, the official Nginx Docker image supports templates with
# variable substitution. But that uses `envsubst`, which does not allow for
# defaults for missing variables. Here, first use the regular command shell
# to set the defaults:
export BACKEND_FASTCGI=${BACKEND_FASTCGI:-php:9000}
export SERVER_NAME=${SERVER_NAME:-localhost}

# Due to `set -u` this would fail if not defined and no default was set above
echo "Will use backend fastcgi to ${BACKEND_FASTCGI}*"
echo "Will use server name as ${SERVER_NAME}*"

# Finally, let the original Nginx entry point do its work, passing whatever is
# set for CMD. Use `exec` to replace the current process, to trap any signals
# (like Ctrl+C) that Docker may send it:
exec /docker-entrypoint.sh "$@"
