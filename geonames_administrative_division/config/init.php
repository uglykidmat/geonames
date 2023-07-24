<?php
//_______________________ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//_______________________FUNCTIONS
require (__DIR__ . '/../functions.php');
require (__DIR__ . '/../../vendor/autoload.php');

//_______________________DATABASE
define('_DB_PROD_SERVER_', '127.0.0.1');
define('_DB_PROD_NAME_', 'tools');
define('_DB_PROD_USER_', 'root');
define('_DB_PROD_PASSWD_', '');

define('_DB_PNC_NAME_', 'admin_data');
