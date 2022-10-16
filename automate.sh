DIRPATH="/var/www/html/backend.gdpr.se"
cd $DIRPATH
GITPASS="@VLXSaea_rr"
spawn ./spawn.sh
expect "Enter passphrase for key '/home/gdpr_root/.ssh/id_ed25519':\n"
send -- $GITPASS
php artisan migrate --force
php artisan db:seed --force