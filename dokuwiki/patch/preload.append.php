// <?php
/* this code will be appended to dokuwiki's inc/preload.php from Dockerfile */

array_push($config_cascade['main']['protected'], '/var/www/html/conf.core/conf.farm-baseurl.php');

