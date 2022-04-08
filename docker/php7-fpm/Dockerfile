# See https://github.com/docker-library/php/blob/4677ca134fe48d20c820a19becb99198824d78e3/7.0/fpm/Dockerfile
FROM php:7.2-fpm

MAINTAINER Maxence POUTORD <maxence.poutord@gmail.com>



RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libcurl4-gnutls-dev \
    libxml2-dev \
    libmcrypt-dev \
    libpng-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev 
 
 
#   libjpeg-dev \
    

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Rome /etc/localtime
RUN "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ --with-freetype-dir=/usr/include \
#&& docker-php-ext-install pdo pdo_mysql curl xml intl mcrypt gd bcmath mbstring zip soap
&& docker-php-ext-install pdo pdo_mysql curl xml intl gd bcmath mbstring zip soap

#RUN pecl install -o -f redis \
RUN printf "\n" | pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

RUN pecl install mcrypt-1.0.4
RUN docker-php-ext-enable mcrypt

RUN echo "memory_limit = -1" >> /usr/local/etc/php/php.ini
RUN echo "upload_max_filesize = 10M;" >> /usr/local/etc/php/php.ini

# install xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


RUN echo 'alias sf="php app/console"' >> ~/.bashrc
RUN echo 'alias sf3="php bin/console"' >> ~/.bashrc

RUN usermod -u 1000 www-data

WORKDIR /var/www/symfony
