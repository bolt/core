# https://github.com/docker-library/php/blob/master/7.3/stretch/fpm/Dockerfile
FROM php:7.3-fpm

ENV PATH /path/to/bin/folder:$PATH
ENV PHP_MEMORYLIMIT 2048M
ENV PHP_TIMEZONE Europe/Warsaw
ENV PHP_MAXFILESIZE 100M
ENV PHP_MAXEXECTIME 300
ARG CONFD_VERSION
ENV CONFD_VERSION ${CONFD_VERSION:-0.11.0}
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN extBuildDeps=" \
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
            ttf-freefont \
            fontconfig \
            dbus \
            openssh-client \
            libzip-dev \
            sendmail && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure gd --enable-gd-native-ttf --with-jpeg-dir=/usr/lib/x86_64-linux-gnu --with-png-dir=/usr/lib/x86_64-linux-gnu --with-freetype-dir=/usr/lib/x86_64-linux-gnu && \
    docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-install \
            exif \
            pdo \
            pdo_mysql \
            zip \
            intl \
            iconv \
            mbstring \
            gd \
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
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin —-filename=composer && \
    mv /usr/local/bin/composer.phar /usr/local/bin/composer && \
    composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --optimize-autoloader --classmap-authoritative && \
    composer clear-cache && \
    mkdir -p /root/.ssh/ && \
    ssh-keyscan github.com >> /root/.ssh/known_hosts

EXPOSE 9000
COPY confd/ /etc/confd/
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 755 /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]