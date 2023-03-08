#!/bin/bash
USER=DB_ADMIN
OUT=db_dump.sql
read -s -p "Enter MySQL password for $USER: " mypassword
mysqldump --databases DB_NAME > $OUT --user=$USER --password=$mypassword
echo "Wrote $OUT"