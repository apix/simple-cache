#!/bin/bash
set -e
set -o pipefail

VERSION=`phpenv version-name`

if [ "${VERSION}" = "hhvm" ]; then
    PHPINI=/etc/hhvm/php.ini
else
    PHPINI=~/.phpenv/versions/$VERSION/etc/php.ini
fi

# enable xdebug
if [[ $TRAVIS_PHP_VERSION =~ ^hhvm ]]
then
    echo 'xdebug.enable = On' >> $PHPINI
fi
