# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8
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
		gnu-libiconv \
		jq \
		redis \
		openrc \
		busybox-extras \
	;

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

ARG APCU_VERSION=5.1.20
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
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
		redis.so \
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

#RUN apk add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data
#RUN usermod -u 1000 www-data

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

VOLUME /var/run/php

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

# Allow to use development versions of Symfony
ARG STABILITY="stable"
ENV STABILITY ${STABILITY:-stable}

# Allow to select skeleton version
ARG SYMFONY_VERSION=""

# Download the Symfony skeleton and leverage Docker cache layers
#RUN composer create-project "symfony/skeleton ${SYMFONY_VERSION}" . --stability=$STABILITY --prefer-dist --no-dev --no-progress --no-interaction; \
#	composer clear-cache

###> recipes ###
###< recipes ###

COPY . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync

#add jwt token
RUN set -e; \
	apk add openssl; \
	mkdir -p config/jwt; \
	jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}; \
	echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096; \
	echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout; \
	setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt; \
	setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt; \
    chmod 664 config/jwt/private.pem

#add composer install
RUN  /usr/bin/composer install

VOLUME /srv/app/var

#expose port and add user

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

COPY --from=dunglas/mercure:v0.11 /srv/public /srv/mercure-assets/
COPY --from=symfony_caddy_builder /usr/bin/caddy /usr/bin/caddy
COPY --from=symfony_php /srv/app/public public/
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
RUN mkdir /etc/caddy/key; \
    mkdir /etc/caddy/cert
COPY certs/cert.pem /etc/caddy/cert/cert.pem
COPY certs/key.pem /etc/caddy/key/key.pem

# nginx stage
FROM alpine:latest AS symfony_nginx

RUN rm -rf /var/cache/apk/* && \
    rm -rf /tmp/*

RUN apk update

RUN apk add --update nginx
RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*
RUN mkdir -p /var/cache/nginx/iam
COPY certs/cert.pem /etc/certs/live/iam/fullchain.pem
COPY certs/key.pem /etc/certs/live/iam/privkey.pem
COPY docker/nginx/nginx.conf /etc/nginx/
COPY docker/nginx/upstream.conf /etc/nginx/default/

RUN adduser -D -g '' -G www-data www-data
#RUN apk add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data
#RUN usermod -u 1000 www-data

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
