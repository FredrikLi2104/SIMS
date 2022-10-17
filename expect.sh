#!/usr/bin/expect -f
spawn ./spawn.sh
expect -exact "Enter passphrase for key '/home/gdpr_root/.ssh/id_ed25519': "
send -- "@VLXSaea_rr\r"