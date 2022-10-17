#!/usr/bin/expect -f
spawn git remote pull origin master
expect "password: "
send "PASSWORD\r"
php artisan migrate --force
php artisan db:seed --force