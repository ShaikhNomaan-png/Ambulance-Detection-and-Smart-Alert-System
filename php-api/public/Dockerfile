FROM php:8.1-apache

# enable mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql

# enable file uploads and increase limits if desired
RUN a2enmod rewrite

WORKDIR /var/www/html

# copy public files into image (volume will override in compose)
COPY public/ /var/www/html/

# create snapshots dir
RUN mkdir -p /var/www/html/snapshots && chown -R www-data:www-data /var/www/html/snapshots

EXPOSE 80
