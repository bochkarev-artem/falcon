#!/usr/bin/env bash
composer install
php bin/console assetic:dump --env=dev