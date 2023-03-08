# ONTIS - Image Annotation Tool

## Setup Data and Tool

1. Create a new folder `./web/images` (Hint: can also be a symlink)
2. Collect images to annotate and place them into `./web/images`

## Setup Database

1. Install [MySQL](https://dev.mysql.com/doc/mysql-getting-started/en/)
2. Install [Python 3](https://www.python.org/)
3. Setup the database using `setup.sh`. Hint: `setup.sh` installs a new database/database user and will overwrite existing databases IF they have the same credentials. Also, the script requires the user to use a privileged database account (referred to as admin) that is able to create users and databases

## Setup Webserver

1. Setup web server: install a web server (e.g. [Apache](https://httpd.apache.org/)).
2. Copy (or symlink) all contents of `./` into the document root of your web server under a memorable name, e.g. `doc_root/ontis-tool` (Hint: `./install` and all configs besides `./config/config.php` can be omitted, specifically when 4. is skipped)
3. Make sure to set the correct permissions for the web server
    - **read/write**: `./web/tmp` (temporary directory for file exports)
    - **read**: `./config/config.php`(generated configuration file, **IMPORTANT**: never delete/overwrite this file as it contains the DB configuration, such as db and password)
4. Secure `./config` folder -- make sure all rules contained in `./config/.htaccess` are executed. You might need to allow this in your server's `httpd.conf` / `apache2.conf` file via:

    ```
    # Allow '.htaccess' overrides in ontis-tool folder:
    <Directory /var/www/html/ontis-tool>
        AllowOverride All
    </Directory>
    ```

5. Simply run the application using `[SERVER_LOCATION]/ontis-tool`, e.g. `http://localhost/ontis-tool/`

## Backup/Restore

1. Backup your `./config` folder (Optional but necessary in case you plan to run `setup.sh` using the same database name)
2. Use/adjust `./config/dump_db.sh` to create an export of the current data (The default output is `./config/db_dump.sql`)
3. Clone the repository at another location (Skip this step if you simply want to overwrite the current database)
4. Run `./setup.sh` at to recreate an empty database and a new config folder (Be sure to use the same name database name used when running the backup in step 2)
5. Copy Run `./config/restore_db.sh` and `./config/db_dump.sql` to the new config folder to restore the exported database into the newly created one
