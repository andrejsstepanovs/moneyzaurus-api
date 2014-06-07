#!/bin/sh
# https://github.com/facebook/hhvm/wiki/fastcgi

echo $WEB_SERVER_DOCROOT

php --version

php -S localhost:8000 -t ./ &
