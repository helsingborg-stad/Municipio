#!/bin/bash
npm install
npm run build
composer install --prefer-dist --no-progress --no-suggest 

# any unique commands for this plugin