#!/bin/sh
set -e

if [ "$1" = 'frankenphp' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	# Install the project the first time PHP is started
  sleep 3
	if [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
		composer install --ignore-platform-req=ext-mongodb --prefer-dist --no-progress --no-interaction
	fi

	if [ "$( find ./migrations -iname '*.php' -print -quit )" ]; then
		php bin/console doctrine:migrations:migrate --no-interaction
	fi

fi

exec docker-php-entrypoint "$@"