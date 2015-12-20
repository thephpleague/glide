FROM php:7.0-apache
# Install modules
RUN apt-get update && apt-get install --no-install-recommends -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        imagemagick \
        libmagickwand-6.q16-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip \
    && apt-get install --no-install-recommends -y git \
    && git clone https://github.com/mkoppanen/imagick.git /root/imagick \
    && cd /root/imagick \
    && phpize \
    && ./configure --with-php-config=/usr/local/bin/php-config \
    && make \
    && make install \
    && cd \
    && rm -Rf /root/imagick \
    && mkdir -p /var/lib/glide/master \
    && mkdir -p /var/lib/glide/cache \
    && chown www-data:www-data /var/lib/glide/cache \
    && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/imagick.ini \
    && a2enmod rewrite \
    && service apache2 restart \
    && cd /var/www \
    && /usr/bin/curl -sS https://getcomposer.org/installer | /usr/local/bin/php \
    && mv composer.phar /usr/local/bin/composer \
    && composer require league/glide \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY images/ /var/lib/glide/master/
COPY ext/ /var/www/html/
