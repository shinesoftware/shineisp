#!/bin/bash 

echo "ShineISP Update and Reset tasks.";
echo "================================";
echo "";
echo "GIT Update";
echo "==========";
cd /var/www/shineisp.com/web/demo/ 
git fetch --all
git reset --hard origin/master

echo "";

echo "Setting the permissions for ShineISP";
echo "====================================";
cd /var/www/shineisp.com/web/demo/ 
chown web3713 . -R && chgrp client1 . -R
chmod 776 application/configs/ 
chmod 776 public -R
chmod 776 reset.sh
rm -f public/.htaccess

echo "Finished";
