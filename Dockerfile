#syntax=docker/dockerfile:1.4

# Versions
FROM php:8.2-fpm-alpine AS php_upstream
FROM mlocati/php-extension-installer:2 AS php_extension_installer_upstream
FROM composer/composer:2-bin AS composer_upstream
FROM caddy:2-alpine AS caddy_upstream
FROM nginx:stable-alpine AS nginx_upstream

# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# Base PHP image
FROM php_upstream AS php_base

WORKDIR /srv/app

# persistent / runtime deps
# hadolint ignore=DL3018
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
		jq \
		redis \
		openrc \
		busybox-extras \
	;

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=php_extension_installer_upstream --link /usr/bin/install-php-extensions /usr/local/bin/

RUN set -eux; \
    install-php-extensions \
		apcu \
		intl \
		opcache \
		zip \
		redis \
    ;

###> recipes ###
###> doctrine/doctrine-bundle ###
RUN apk add --no-cache --virtual .pgsql-deps postgresql-dev; \
	docker-php-ext-install -j"$(nproc)" pdo_pgsql; \
	apk add --no-cache --virtual .pgsql-rundeps so:libpq.so.5; \
	apk del .pgsql-deps
###< doctrine/doctrine-bundle ###
###< recipes ###

COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/

COPY --link docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php

COPY --link docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY --link docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer_upstream --link /composer /usr/bin/composer


# Dev PHP image
FROM php_base AS php_dev

ENV APP_ENV=dev XDEBUG_MODE=off
VOLUME /srv/app/var/

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
	install-php-extensions \
    	xdebug \
    ;

COPY --link docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

# Prod PHP image
FROM php_base AS php_prod

ENV APP_ENV=prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

# prevent the reinstallation of vendors at every changes in the source code
COPY --link composer.* symfony.* ./
RUN set -eux; \
	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# copy sources
COPY --link . ./
RUN rm -Rf docker/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;

# Cronjob
RUN echo "*/30 * * * * /srv/app/bin/console gesdinet:jwt:clear > /dev/stdout" >> /etc/crontab

RUN crontab /etc/crontab

# Base Caddy image
FROM caddy_upstream AS caddy_base

ARG TARGETARCH

WORKDIR /srv/app

# Download Caddy compiled with the Mercure and Vulcain modules
ADD --chmod=500 https://caddyserver.com/api/download?os=linux&arch=$TARGETARCH&p=github.com/dunglas/mercure/caddy&p=github.com/dunglas/vulcain/caddy /usr/bin/caddy

COPY --link docker/caddy/Caddyfile /etc/caddy/Caddyfile

# Prod Caddy image
FROM caddy_base AS caddy_prod

COPY --from=php_prod --link /srv/app/public public/

# Base nginx image
FROM nginx_upstream AS nginx_base

# Each time Nginx is started it will perform variable substition in all template
# files found in `/etc/nginx/templates/*.template`, and copy the results (without
# the `.template` suffix) into `/etc/nginx/conf.d/`. Below, this will replace the
# original `/etc/nginx/conf.d/default.conf`; see https://hub.docker.com/_/nginx
COPY --link docker/nginx/nginx.conf /etc/nginx/
COPY --link docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY --link docker/nginx/docker-defaults.sh /
RUN mkdir -p /var/cache/nginx/iam

# Just in case the file mode was not properly set in Git
RUN chmod +x /docker-defaults.sh

# This will delegate to the original Nginx `docker-entrypoint.sh`
ENTRYPOINT ["/docker-defaults.sh"]

WORKDIR /srv/app

RUN adduser -D -g '' -G www-data www-data
#RUN apk add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data
#RUN usermod -u 1000 www-data

# The default parameters to ENTRYPOINT (unless overruled on the command line)
CMD ["nginx", "-g", "daemon off;"]

EXPOSE 80
EXPOSE 443

# Prod nginx image
FROM nginx_base AS nginx_prod

COPY --from=php_prod --link /srv/app/public public/

