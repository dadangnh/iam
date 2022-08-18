# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8.1
ARG CADDY_VERSION=2

# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine AS symfony_php

# persistent / runtime deps
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

ARG APCU_VERSION=5.1.21
# skipcq: DOK-DL4006
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
		postgresql-libs \
		postgresql-dev \
		pcre-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		zip \
		pgsql \
		pdo pdo_pgsql \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
		redis \
		xdebug \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
		redis.so \
		xdebug \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

VOLUME /var/run/php

# skipcq: DOK-DL3022
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

# Allow to choose skeleton
ARG SKELETON="symfony/skeleton"
ENV SKELETON ${SKELETON}

# Allow to use development versions of Symfony
ARG STABILITY="stable"
ENV STABILITY ${STABILITY}

# Allow to select skeleton version
ARG SYMFONY_VERSION=""
ENV SYMFONY_VERSION ${SYMFONY_VERSION}

# Download the Symfony skeleton and leverage Docker cache layers
#RUN composer create-project "${SKELETON} ${SYMFONY_VERSION}" . --stability=$STABILITY --prefer-dist --no-dev --no-progress --no-interaction; \
#	composer clear-cache

###> recipes ###
###> doctrine/doctrine-bundle ###
RUN apk add --no-cache --virtual .pgsql-deps postgresql-dev; \
	docker-php-ext-install -j$(nproc) pdo_pgsql; \
	apk add --no-cache --virtual .pgsql-rundeps so:libpq.so.5; \
	apk del .pgsql-deps
###< doctrine/doctrine-bundle ###
###< recipes ###

COPY . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /srv/app/var

RUN apk add --no-cache busybox-initscripts

RUN echo "*/30 * * * * /srv/app/bin/console gesdinet:jwt:clear > /dev/stdout" >> /etc/crontab

RUN crontab /etc/crontab

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM caddy:${CADDY_VERSION}-builder-alpine AS symfony_caddy_builder

RUN xcaddy build \
	--with github.com/dunglas/mercure \
	--with github.com/dunglas/mercure/caddy \
	--with github.com/dunglas/vulcain \
	--with github.com/dunglas/vulcain/caddy

FROM caddy:${CADDY_VERSION} AS symfony_caddy

WORKDIR /srv/app

# skipcq: DOK-DL3022
COPY --from=dunglas/mercure:v0.11 /srv/public /srv/mercure-assets/
# skipcq: DOK-DL3022
COPY --from=symfony_caddy_builder /usr/bin/caddy /usr/bin/caddy
# skipcq: DOK-DL3022
COPY --from=symfony_php /srv/app/public public/
# skipcq: DOK-DL3022
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile

# Disable the SSL cert injection
#RUN mkdir /etc/caddy/key; \
#    mkdir /etc/caddy/cert
#COPY certs/cert.pem /etc/caddy/cert/cert.pem
#COPY certs/key.pem /etc/caddy/key/key.pem



# nginx stage
FROM nginx:stable-alpine AS symfony_nginx

# Each time Nginx is started it will perform variable substition in all template
# files found in `/etc/nginx/templates/*.template`, and copy the results (without
# the `.template` suffix) into `/etc/nginx/conf.d/`. Below, this will replace the
# original `/etc/nginx/conf.d/default.conf`; see https://hub.docker.com/_/nginx
COPY docker/nginx/nginx.conf /etc/nginx/
COPY docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY docker/nginx/docker-defaults.sh /
RUN mkdir -p /var/cache/nginx/iam

# Just in case the file mode was not properly set in Git
RUN chmod +x /docker-defaults.sh

# This will delegate to the original Nginx `docker-entrypoint.sh`
ENTRYPOINT ["/docker-defaults.sh"]

WORKDIR /srv/app

RUN adduser -D -g '' -G www-data www-data
#RUN apk add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data
#RUN usermod -u 1000 www-data
COPY --from=symfony_php /srv/app .

# The default parameters to ENTRYPOINT (unless overruled on the command line)
CMD ["nginx", "-g", "daemon off;"]

EXPOSE 80
EXPOSE 443
