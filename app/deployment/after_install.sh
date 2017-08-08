#!/usr/bin/env bash
cd /home/ubuntu/falcon/
export COMPOSER_HOME=./
composer install -o --no-dev
php bin/console assetic:dump --env=prod
rm -rf ./var/cache/*
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media
php bin/console doctrine:migrations:migrate
cd /home/ubuntu/falcon/web
unlink images
ln -s ../../falcon-images/images images
cd /home/ubuntu/falcon/
chmod +x /home/ubuntu/falcon/app/jobs/update_books.sh
rm -rf ./web/sitemap.en/sitemap.xml
php bin/console presta:sitemaps:dump web/sitemap.en/ --section=en --base-url=http://bookary.eu/ --gzip --env=prod --no-debug
rm -rf ./web/sitemap.ru/sitemap.xml
php bin/console presta:sitemaps:dump web/sitemap.ru/ --section=ru --base-url=http://bookary.ru/ --gzip --env=prod --no-debug
