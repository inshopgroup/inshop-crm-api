FROM php:7.4.5-fpm

WORKDIR /var/www

RUN usermod -u 1000 www-data

RUN apt-get update
RUN apt-get install -y \
    nano \
    curl \
    wget \
    git \
    zip \
    unzip \
    libicu-dev \
    libpq-dev \
    gnupg

# wkhtmltopdf
RUN apt-get install -y \
    libxrender1 \
    libfontconfig1 \
    libx11-dev \
    libjpeg62 \
    libxtst6 \
    fontconfig \
    xfonts-75dpi \
    xfonts-base
RUN wget "https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.stretch_amd64.deb"
RUN dpkg -i wkhtmltox_0.12.5-1.stretch_amd64.deb
RUN apt-get -f install

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# GD
RUN apt-get install -y \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include \
    --with-jpeg=/usr/include/

RUN docker-php-ext-install gd

# other php extensions
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install intl
RUN docker-php-ext-enable opcache

# redis
RUN pecl install redis
RUN docker-php-ext-enable redis

# php.ini
RUN echo "date.timezone=UTC" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "error_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "display_errors=Off" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "memory_limit = 2048M" >> /usr/local/etc/php/conf.d/docker-php-custom.ini

RUN echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/docker-php-custom.ini

RUN echo "opcache.preload_user=www-data" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "opcache.preload=/var/www/config/preload.php" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "opcache.interned_strings_buffer=32" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "opcache.max_accelerated_files = 33000" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "realpath_cache_size = 4096K" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "realpath_cache_ttl = 600" >> /usr/local/etc/php/conf.d/docker-php-custom.ini

#clean
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
