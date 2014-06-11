# DOCKER-VERSION 0.3.4

# sudo docker build -t wormhit/moneyzaurus-api .
# sudo docker pull wormhit/moneyzaurus-api
# sudo docker run -d wormhit/moneyzaurus-api
# sudo docker ps
# sudo docker inspect XXXX | grep IPAddress
# sudo docker run -i -t wormhit/moneyzaurus-api /bin/bash
# sudo systemctl stop docker

# sudo docker pull wormhit/moneyzaurus-api
# sudo docker run -d wormhit/moneyzaurus-api

FROM ubuntu:14.04

MAINTAINER Andrejs Stepanovs <andrejsstepanovs@gmail.com>

RUN apt-get update
RUN apt-get install -y git php5-fpm php5 php5-cli php5-curl php5-sqlite php5-intl nginx vim wget curl sqlite3

RUN git clone https://github.com/wormhit/moneyzaurus-api.git /var/www -b docker

RUN /usr/bin/php /var/www/composer.phar install --working-dir /var/www

RUN cp /var/www/data/nginx.conf /etc/nginx/nginx.conf

EXPOSE  80

CMD service php5-fpm start && service nginx start
