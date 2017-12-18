#!/usr/bin/env bash
cd /home/ubuntu/falcon/
php bin/console app:update-litres-data books stream
sleep 10s
php bin/console app:update-featured-menu
sleep 1m
php bin/console app:schedule-book-index --env=prod
rm -rf ./web/sitemap/*
php bin/console presta:sitemaps:dump web/sitemap/ --base-url=http://bookary.ru/ --gzip --env=prod --no-debug
