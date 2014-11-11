FROM tutum/nginx:latest

# Install packages
RUN apt-get update
RUN apt-get install -y supervisor curl php5-cli php5-fpm php5-mysql php5-sqlite php5-curl php5-intl

# Create required directories
RUN mkdir -p /var/log/supervisor
RUN mkdir -p /etc/nginx
RUN mkdir -p /var/run/php5-fpm


RUN touch /etc/supervisor/conf.d/supervisord.conf
RUN echo "[supervisord]" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "nodaemon=true" >> /etc/supervisor/conf.d/supervisord.conf

RUN echo "[program:nginx]" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "command = /usr/sbin/nginx" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "user = root" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "autostart = true" >> /etc/supervisor/conf.d/supervisord.conf

RUN echo "[program:php5-fpm]" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "command = /usr/sbin/php5-fpm" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "user = root" >> /etc/supervisor/conf.d/supervisord.conf
RUN echo "autostart = true" >> /etc/supervisor/conf.d/supervisord.conf

ADD ./data/nginx.conf /etc/nginx/sites-enabled/default
ADD ./ /var/www/

RUN /usr/bin/php /var/www/composer.phar install --working-dir /var/www/
RUN /var/www/vendor/bin/phpunit -c /var/www/tests/unit/phpunit.xml

RUN /var/www/tests/ci/start-php.sh
RUN /var/www/vendor/bin/phpunit -c /var/www/tests/acceptance/phpunit.xml


EXPOSE 80 9000

CMD ["/usr/bin/supervisord"]