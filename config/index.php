<?php

/**
 * This file is ONLY a precaution to prevent users to trigger the servers' file listing functionality. Please make sure '.htaccess' is working properly, e.g. by including the following in your 'httpd.conf' (or 'apache2.conf') file:
 * 
 *  # Allow '.htaccess' overrides in ontis-tool folder:
 *   <Directory /var/www/html/ontis-tool>
 *       AllowOverride All
 *   </Directory>
 *
 */



redirect('../web/index.php');

function redirect($url, $statusCode = 303)
{
   header('Location: ' . $url, true, $statusCode);
   die();
}

?>