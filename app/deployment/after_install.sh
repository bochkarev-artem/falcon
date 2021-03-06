#!/usr/bin/env bash
cd /home/falcon/
export COMPOSER_HOME=./
composer install -o --no-dev
rm -rf ./var/cache/*
php bin/console assetic:dump --env=prod
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media
php bin/console doctrine:migrations:migrate
cd /home/falcon/web
unlink images
ln -s ../../falcon-images/images images
cd /home/falcon/
chmod +x /home/falcon/app/jobs/update_books.sh
