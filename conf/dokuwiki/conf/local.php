<?php
/*
 * Dokuwiki Configuration File - Local Settings
 */

$conf['title'] = 'Open CourseWare CS@UPB';
$conf['tagline'] = '';
$conf['license'] = 'cc-by-sa';
$conf['template'] = 'arctic-cs';
$conf['useacl'] = 1;
$conf['superuser'] = '@admin';
$conf['disableactions'] = 'register';
$conf['userewrite'] = 1;
$conf['useslash'] = 1;
$conf['useheading'] = 1;

// disable compress when developing templates
/* $conf['compress'] = 0; */

// enable inline <html>
$conf['plugin']['htmlok']['htmlok'] = 1;
$conf['htmlok'] = '1';

/* Template customizations */
$conf['tpl']['arctic-cs']['sidebar'] = 'right';

