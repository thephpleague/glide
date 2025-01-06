FROM php:8.1-cli

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# install dependencies
RUN apt update && apt install -y --no-install-recommends \
        libexif-dev \
        git \
        unzip \
        zip

COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install PHP extensions
RUN install-php-extensions zip gd imagick exif xdebug
