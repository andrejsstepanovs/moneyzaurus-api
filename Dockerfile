FROM php:5.5-apache
MAINTAINER Andrejs Stepanovs <andrejsstepanovs@gmail.com>

RUN a2enmod rewrite

RUN apt-get update
RUN apt-get install -y php5-cli php5-sqlite php5-intl sqlite3

RUN git clone https://github.com/wormhit/moneyzaurus-api.git /var/www/html/
RUN /usr/bin/php /var/www/composer.phar install --working-dir /var/www/html/

#ADD ./ /var/www/html/