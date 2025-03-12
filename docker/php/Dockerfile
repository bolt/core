# https://github.com/docker-library/php/blob/master/8.1/bookworm/fpm/Dockerfile
FROM php:8.1-fpm-bookworm

ENV PHP_MEMORYLIMIT=2048M
ENV PHP_TIMEZONE=Europe/Warsaw
ENV PHP_MAXFILESIZE=100M
ENV PHP_MAXEXECTIME=300
ARG CONFD_VERSION
ENV CONFD_VERSION=${CONFD_VERSION:-0.11.0}
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN --mount=type=cache,target=/var/cache/apt \
    --mount=type=cache,target=/var/lib/apt \
    extBuildDeps=" \
        libfreetype6 \
		libfreetype6-dev \
        libpng-dev \
        libjpeg62-turbo \
        libjpeg-dev \
        libicu-dev \
        libmcrypt4 \
		libmcrypt-dev \
		libldap2-dev \
		zlib1g-dev \
		libavif-dev \
		libwebp-dev \
		libpq-dev \
    	libonig-dev \
	"; \
	set -x \
    && usermod -u 1000 www-data \
	&& apt-get update && apt-get install -y --no-install-recommends $extBuildDeps \
            git \
            nano \
            bash \
            xvfb \
            wget \
            zip unzip \
            acl \
            iproute2 \
            fonts-freefont-ttf \
            fontconfig \
            dbus \
            openssh-client \
            libzip-dev \
            sendmail && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure gd --enable-gd --with-webp --with-avif --with-jpeg --with-freetype && \
    docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-install \
            exif \
            pdo \
            pdo_mysql \
            pdo_pgsql \
            zip \
            intl \
            mbstring \
            gd \
            pcntl \
            opcache && \
    wget --progress=bar:force --retry-connrefused -t 5 \
    https://github.com/kelseyhightower/confd/releases/download/v${CONFD_VERSION}/confd-${CONFD_VERSION}-linux-amd64 -O /bin/confd && \
	chmod +x /bin/confd && \
	sed -i s'/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i s'/;listen.owner = nobody/listen.owner = nginx/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i s'/;listen.group = nobody/listen.group = nginx/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i s'/;listen.mode = 0660/listen.mode = 0660/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i s'/;listen.mode = 0660/listen.mode = 0660/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i s'/pm.max_children = 5/pm.max_children = 50/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i s'/pm.start_servers = 2/pm.start_servers = 5/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i s'/pm.min_spare_servers = 1/pm.min_spare_servers = 5/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i s'/pm.max_spare_servers = 3/pm.max_spare_servers = 35/' /usr/local/etc/php-fpm.d/www.conf && \
    echo 'ForwardAgent yes' >> /etc/ssh/ssh_config  && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer clear-cache

EXPOSE 9000
COPY confd/ /etc/confd/
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 755 /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
