FROM php:7.2-apache
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libicu-dev \
        libcurl4-openssl-dev \
        libxml2-dev \
        libonig-dev \
        libzip-dev \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql intl mbstring curl xml zip bcmath soap

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Rome /etc/localtime
RUN "date"

# RUN echo "http://192.168.14.11/owncloud/remot:q!e.php/webdav /var/www/edilfoto/owncloud:q!:wq:wq: davfs rw,user,noauto 0 0" >> /etc/fstab
# RUN ::wq
WORKDIR /var/www/bamboo
