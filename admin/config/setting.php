<?php
/////////////////////////////////
// rutas de acceso
///////////////////////////////// 
DEFINE("root", $_SERVER["DOCUMENT_ROOT"]);              // raíz
DEFINE("self", $_SERVER["PHP_SELF"]);                   // directiva SELF

/////////////////////////////////////////
// CONTROLES DE DIRECTORIOS
/////////////////////////////////////////
DEFINE("SITE", 'http://'.$_SERVER['SERVER_NAME']);
// sin htaccess
// DEFINE("SITE_URL", SITE.'/index.php/');
// DEFINE("ADMIN_SITE", SITE.'/admin/index.php/');
// con htaccess
DEFINE("SITE_URL", SITE.'/index.php/');
DEFINE("ADMIN_SITE", SITE.'/admin/');

DEFINE("CONTROLLER", 'controller/bloques/');
DEFINE("MODEL", 'model/bloques/');
DEFINE("VIEW", 'view/bloques/');
DEFINE("TEMPLATE", './update/template');
DEFINE("STATIC_URL", SITE.'/update/static/');

/////////////////////////////////////////
// WEBMASTER
/////////////////////////////////////////
DEFINE("app_name","TuOrden");
DEFINE("mail_postmaster","info@rogerca.com");

include 'setting-edit.php';
?>