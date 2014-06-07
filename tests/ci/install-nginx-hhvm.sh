#!/bin/sh
# https://github.com/facebook/hhvm/wiki/fastcgi

hhvm --version

sudo apt-get install nginx

# Configure apache virtual hosts
sudo cp -f tests/ci/nginx.conf /etc/nginx/
sudo sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/nginx/nginx.conf

hhvm --mode server -vServer.Type=fastcgi -vServer.Port=8100 >/dev/null 2>&1 & echo $!
sudo service nginx start