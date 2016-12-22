#!/usr/bin/env bash
cd /home/ubuntu/falcon/
export COMPOSER_HOME=./
composer install
php bin/console assetic:dump --env=dev