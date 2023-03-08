#!/bin/bash

# python check
version=$(python -V 2>&1 | grep -Po '(?<=Python )(.+)')
parsedVersion=$(echo "${version//./}")
if [[ "$parsedVersion" -lt "350" ]]
then
    echo "Setup has found Python version: $version"
    echo "Please install Python >= 3.5"
    exit 1
fi

MSG=$'WARNING: Running setup overwrites existing config files and databases (if names are equal).\nAre you sure to continue (y/N)?'
read -p "$MSG" -n 1 -r
echo    # (optional) move to a new line
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    echo "Aborted."
    exit 1
fi

# create cfgs
cd install
python -m pip install --user -r requirements.txt
python create_config.py
cd ..

# create database
DB_PREPARE_EXEC=./config/prepare_db.sh
if test ! -f "$DB_PREPARE_EXEC"; then
    echo "Error file not found: $DB_PREPARE_EXEC"
    exit 1
fi
source $DB_PREPARE_EXEC