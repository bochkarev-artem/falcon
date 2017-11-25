#!/usr/bin/env bash
cd /home/ubuntu/falcon/
php bin/console app:update-litres-data books stream
php bin/console app:update-featured-menu
php bin/console app:schedule-book-index --env=prod
