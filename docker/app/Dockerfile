# https://github.com/docker-library/php/blob/master/7.3/buster/fpm/Dockerfile
ARG PHP_VERSION=7.3

FROM php:${PHP_VERSION}-fpm-alpine AS bolt_php

ENV PHP_MEMORYLIMIT=2048M
ENV PHP_TIMEZONE=Europe/Warsaw
ENV PHP_MAXFILESIZE=100M
ENV PHP_MAXEXECTIME=300
ENV COMPOSER_ALLOW_SUPERUSER=1
ARG PDO_DRIVERS=pdo_mysql

WORKDIR /var/www/bolt

RUN set -eux ; \
            apk add --no-cache --update git \
            bash \
            wget \
            zip \
            acl \
            ttf-freefont \
            openssh-client  \
            fontconfig \
            dbus \
            zlib-dev \
            libzip-dev  \
            icu-dev \
            freetype-dev \
            libjpeg-turbo-dev \
            libpng-dev \
            openssh-client && \
            docker-php-ext-install -j$(nproc) \
            exif \
            pdo \
            zip \
            intl \
            iconv \
            mbstring \
            gd \
            pcntl \
            $PDO_DRIVERS \
            opcache && \
            echo 'ForwardAgent yes' >> /etc/ssh/ssh_config  && \
            mkdir -p /root/.ssh/ && \
            ssh-keyscan github.com >> /root/.ssh/known_hosts

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --optimize-autoloader --classmap-authoritative && composer install -vvv --profile --optimize-autoloader --classmap-authoritative --no-dev && composer clear-cache

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

EXPOSE 9000
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 755 /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

FROM bolt_php AS bolt_php_dev

RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
            && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
            && mkdir -p /tmp/blackfire \
            && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
            && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
            && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
            && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

FROM node:latest as node

WORKDIR /home/node/app

COPY  --from=bolt_php  /var/www/bolt/vendor/bolt/core  ./vendor/bolt/core
RUN cd ./vendor/bolt/core && npm install && npm run build && cd -

FROM nginx:alpine AS nginx

WORKDIR /var/www/bolt

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p public

COPY --from=node  /home/node/app/vendor/bolt/core/public/assets public/assets
