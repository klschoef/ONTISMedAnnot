#!/bin/bash

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

USER=DB_ADMIN
DBNAME=DB_NAME
IN="$SCRIPT_DIR/prepare_db-1.2.sql"
read -s -p "Enter MySQL password for $USER: " mypassword
mysql --user=$USER --password=$mypassword < $IN
echo "Created DB $DBNAME"