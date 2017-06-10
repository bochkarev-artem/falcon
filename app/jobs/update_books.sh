#!/usr/bin/env bash
cd /home/ubuntu/falcon/
php bin/console app:update-litres-data books y 30
php bin/console app:update-book-images
php bin/console app:update-author-images
php bin/console app:update-featured-menu
php bin/console fos:elastica:populate --env=prod --no-debug
php bin/console presta:sitemaps:dump --gzip --env=prod --no-debug
php bin/console cache:clear --env=prod --no-debug