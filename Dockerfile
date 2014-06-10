# DOCKER-VERSION 0.3.4

FROM       ubuntu:14.04

MAINTAINER Andrejs Stepanovs

RUN     apt-get update
RUN     apt-get install -y wget curl php5-fpm php5 php5-cli php5-curl php5-sqlite php5-intl git sqlite3 nginx

RUN     git clone https://github.com/wormhit/moneyzaurus-api.git /var/www
RUN     php /var/www/composer.phar install --working-dir /var/www

RUN wget -O /etc/nginx/sites-available/default https://gist.github.com/darron/6159214/raw/30a60885df6f677bfe6f2ff46078629a8913d0bc/gistfile1.txt
RUN echo "cgi.fix_pathinfo = 0;" >> /etc/php5/fpm/php.ini
RUN echo "daemon off;" >> /etc/nginx/nginx.conf

EXPOSE  80

CMD     service php5-fpm start && nginx