FROM php:5.5-apache
MAINTAINER Andrejs Stepanovs <andrejsstepanovs@gmail.com>

RUN a2enmod rewrite

RUN apt-get update
RUN apt-get install -y php5-cli php5-mysql php5-sqlite php5-curl php5-intl sqlite3

ADD ./ /var/www/html/
RUN /usr/bin/php /var/www/html/composer.phar install --working-dir /var/www/html/