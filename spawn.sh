git pull origin master
echo "pulled"
php artisan migrate --force
echo "migrated"
php artisan db:seed --force
echo "seeded"