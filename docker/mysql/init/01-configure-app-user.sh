#!/bin/sh

set -eu

mysql -uroot -p"$MYSQL_ROOT_PASSWORD" <<-EOSQL
    ALTER USER '$MYSQL_USER'@'%' IDENTIFIED WITH mysql_native_password BY '$MYSQL_PASSWORD';
    CREATE DATABASE IF NOT EXISTS citas_medicas_testing;
    GRANT ALL PRIVILEGES ON citas_medicas_testing.* TO '$MYSQL_USER'@'%';
EOSQL
