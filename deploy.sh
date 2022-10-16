set -e
cd /var/www/html/backend.gdpr.se
echo "Deployment started ..."
ls -la ~/.ssh
#git pull origin master
#only on start or adding a composer package
#composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
#php artisan migrate --force
#php artisan db:seed --force
echo "Deployment finished!"