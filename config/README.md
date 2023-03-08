Contains all generated config files. **DO NOT DELETE** without making a backup.

**WARNING**: make sure the contents of this folder are secured. For maximum security DON'T include `*.sql` or `*.sh` files here. Also configure your server to allow rule overwrites via '.htaccess' files. For example, the following could be included in your serer's `httpd.conf` of `apache2.conf` file:

```
# Allow '.htaccess' overrides in ontis-tool folder:
<Directory /var/www/html/ontis-tool>
    AllowOverride All
</Directory>
```
