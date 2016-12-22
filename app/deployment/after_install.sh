#!/usr/bin/env bash
cd /home/ubuntu/falcon/
composer install
php bin/console assetic:dump --env=dev