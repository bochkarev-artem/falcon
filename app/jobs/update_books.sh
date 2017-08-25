#!/usr/bin/env bash
cd /home/ubuntu/falcon/
php bin/console app:update-litres-data books stream
php bin/console app:update-featured-menu
php bin/console cache:clear --env=prod --no-debug --no-warmup
rm -rf ./web/sitemap.ru/sitemap.xml
php bin/console presta:sitemaps:dump web/sitemap.ru/ --section=ru --base-url=http://bookary.ru/ --gzip --env=prod --no-debug
rm -rf ./web/sitemap.en/sitemap.xml
php bin/console presta:sitemaps:dump web/sitemap.en/ --section=en --base-url=http://bookary.eu/ --gzip --env=prod --no-debug
