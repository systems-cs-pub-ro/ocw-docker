<?php
/*
 * Dokuwiki Configuration File - Local Settings
 */

$conf['title'] = 'OCW';
$conf['license'] = 'cc-by-sa';
$conf['template'] = 'arctic';
$conf['useacl'] = 1;
$conf['superuser'] = '@admin';
$conf['disableactions'] = 'register';
$conf['userewrite'] = 1;
$conf['useslash'] = 1;

// enable inline <html>
$conf['plugin']['htmlok']['htmlok'] = 1;
$conf['htmlok'] = '1';

/* Arctic template customizations */
$conf['tpl']['arctic']['sidebar'] = 'right';
$conf['tpl']['arctic']['main_sidebar_always'] = 0;
$conf['tpl']['arctic']['left_sidebar_content'] = 'main,toc,user,group,namespace';
$conf['tpl']['arctic']['right_sidebar_order'] = 'namespace,toc,user,group';
$conf['tpl']['arctic']['right_sidebar_content'] = 'main,toc,user,group,namespace';
