#!/usr/bin/env bash
cd /home/ubuntu/falcon/
export COMPOSER_HOME=./
composer install
php bin/console assetic:dump --env=dev
php bin/console cache:clear --env=prod --no-debug
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media
php bin/console doctrine:migrations:migrate
cd /home/ubuntu/falcon/web
unlink images
ln -s ../../falcon-images/images images
cd /home/ubuntu/falcon/
php bin/console presta:sitemaps:dump --env=prod --no-debug --section=books
php bin/console presta:sitemaps:dump --env=prod --no-debug --section=books2
php bin/console presta:sitemaps:dump --env=prod --no-debug --section=authors
php bin/console presta:sitemaps:dump --env=prod --no-debug --section=tags
php bin/console presta:sitemaps:dump --env=prod --no-debug --section=genres
php bin/console presta:sitemaps:dump --env=prod --no-debug --section=series