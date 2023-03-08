#!/bin/bash
USER=DB_ADMIN
DBNAME=DB_NAME
IN=db_dump.sql
read -s -p "Enter MySQL password for $USER: " mypassword
mysql --user=$USER --password=$mypassword $DBNAME < $IN
echo "Restored DB $DBNAME"