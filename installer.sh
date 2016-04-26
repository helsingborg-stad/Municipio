#!/bin/bash
mkdir -p wp-content/uploads/cache/blade-cache
php /usr/local/bin/composer update
find . -name package.json -maxdepth 4 -execdir npm install \;