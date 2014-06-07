#!/bin/sh
# https://github.com/facebook/hhvm/wiki/fastcgi

service nginx start

hhvm --version
hhvm --mode server -vServer.Type=fastcgi -vServer.Port=8100 &
sudo service nginx start
