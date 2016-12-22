#!/usr/bin/env bash
cd /home/ubuntu/falcon/
COMPOSER_HOME="./"
composer install
php bin/console assetic:dump --env=dev