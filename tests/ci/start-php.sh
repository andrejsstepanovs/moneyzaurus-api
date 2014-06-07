#!/bin/sh
# https://github.com/facebook/hhvm/wiki/fastcgi

php --version

php -S localhost:8000 -t ./ &
