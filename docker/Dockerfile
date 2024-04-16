#syntax=docker/dockerfile:1.4

# Versions
FROM dunglas/frankenphp:1-php8.3 AS frankenphp_upstream

# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# Base FrankenPHP image
FROM frankenphp_upstream AS frankenphp_base

WORKDIR /app

USER root
ENV SERVER_NAME="http://"
# persistent / runtime deps
# hadolint ignore=DL3008
RUN apt-get update && apt-get install -y --no-install-recommends \
	acl \
	file \
	gettext \
	git \
	&& rm -rf /var/lib/apt/lists/*

RUN set -eux; \
	install-php-extensions \
		@composer \
		apcu \
		intl \
		opcache \
		zip \
    	pdo_mysql \
	;

WORKDIR /var/www/application
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

###> recipes ###
###< recipes ###

COPY --link ./docker/frankenphp/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link --chmod=755 ./docker/frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link ./docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

ENTRYPOINT ["docker-entrypoint"]

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]

# Dev FrankenPHP image
FROM frankenphp_base AS frankenphp_dev

ENV APP_ENV=dev XDEBUG_MODE=off
VOLUME /app/var/

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
	install-php-extensions \
		xdebug \
	;

COPY --link ./docker/frankenphp/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

# Set working directory
WORKDIR /var/www/application

# Add code
COPY ./symfony /var/www/application
#ENV COMPOSER_ALLOW_SUPERUSER=1
#RUN set -eux; \
#	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch" ]