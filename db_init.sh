#!/bin/bash

CONTAINER="33"
#CONTAINER="admin-mysql_db.1.0wgtxijosd6k0no4gmlskxlmw"

DB_NAME="be_181841"
DB_USER="be_181841"
DB_PASSWORD="student"
ROOT_PASSWORD="prestashop"
SHOP_SSL_URL="localhost:18184"
DUMP_NAME="be_181841_dump.sql"

docker exec -it $CONTAINER mysql -p$ROOT_PASSWORD -e "DROP DATABASE IF EXISTS ${DB_NAME};"
docker exec -it $CONTAINER mysql -p$ROOT_PASSWORD -e "CREATE DATABASE ${DB_NAME};"
docker exec -it $CONTAINER mysql -p$ROOT_PASSWORD -e "USE ${DB_NAME};"
docker exec -it $CONTAINER mysql -p$ROOT_PASSWORD -e "CREATE USER IF NOT EXISTS ${DB_USER}@'%' IDENTIFIED BY '${DB_PASSWORD}';"
docker exec -it $CONTAINER mysql -p$ROOT_PASSWORD -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'%';"
docker exec -it $CONTAINER mysql -p$ROOT_PASSWORD -e "FLUSH PRIVILEGES;"

docker exec -i $CONTAINER mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME < ./${DUMP_NAME}
docker exec -i $CONTAINER mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME -e "UPDATE ps_shop_url SET domain_ssl='${SHOP_SSL_URL}' WHERE id_shop_url=1;"
