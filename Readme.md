docker-compose up -d
composer install
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load