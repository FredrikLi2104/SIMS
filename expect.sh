#!/usr/bin/expect -f
chmod 755 spawn.sh
spawn ./spawn.sh
expect -exact "Enter passphrase for key '/home/gdpr_root/.ssh/id_ed25519': "
send -- "@VLXSaea_rr"
php artisan migrate --force
php artisan db:seed --force