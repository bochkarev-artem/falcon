#!/usr/bin/env bash
cd /home/falcon/
composer install
php bin/console assetic:dump --env=dev