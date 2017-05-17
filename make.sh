#!/bin/bash

# Set up the LAMP stack, and some other tools needed
sudo apt-get install apache2 mysql-server libapache2-mod-php php-mcrypt \
	php-mysql wget phpunit
sudo systemctl restart apache2


# TODO: Add functionality for a repeated entry of the password and ensure
# it is the same as the first time
read -s -p "Enter Password for tut4 mysql: " TUTFOURPW
echo ""
echo "Thanks! Please do not lose"

echo "[client]
user=tut4
password=$TUTFOURPW
mysql_server=localhost
" > .my.cnf

#echo "Enter root mysql password when probed"
read -s -p "Enter the root mysql password: " ROOTPW

# NOTE:
# While it is less secure to use the `--password=` argument
# but it does prevent multiple password promts to the user
# the output can be suppressed by storing it in a variable

# check if the visiochess user is already in the database
HAS_VISIO_USER="$(mysql -uroot -p$ROOTPW -sse "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = 'tut4')")"

if [ $HAS_VISIO_USER == 1 ]
then
	echo "user exists :) will simply update password."
	mysql -uroot -p$ROOTPW -sse "SET PASSWORD FOR 'tut4'@'localhost' = '${TUTFOURPW}'"
else
	mysql -uroot -p$ROOTPW -sse "CREATE USER 'tut4'@'localhost' IDENTIFIED BY '${TUTFOURPW}'"
	mysql -uroot -p$ROOTPW -sse "GRANT ALL PRIVILEGES ON * . * TO 'tut4'@'localhost'"
	mysql -uroot -p$ROOTPW -sse "FLUSH PRIVILEGES;"
fi

time php -f php/create_default_db.php
time python py/populate.py .my.cnf
