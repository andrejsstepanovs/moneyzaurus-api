#!/bin/sh
# https://github.com/facebook/hhvm/wiki/fastcgi

hhvm --version

apt-get install nginx

# Configure apache virtual hosts
cp -f tests/ci/nginx.conf /etc/nginx/
sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/nginx/nginx.conf
