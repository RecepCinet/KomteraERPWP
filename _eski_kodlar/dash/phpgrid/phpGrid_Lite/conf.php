<?php
define('PHPGRID_DB_HOSTNAME','localhost'); 
define('PHPGRID_DB_USERNAME', 'root');    
define('PHPGRID_DB_PASSWORD', 'root'); 
define('PHPGRID_DB_NAME', 'sampledb');
define('PHPGRID_DB_TYPE', 'mysqli'); 
define('PHPGRID_DB_CHARSET','utf8mb4');



// *** You should only define SERVER_ROOT manually when use Apache alias directive or IIS virtual directory ***
define('SERVER_ROOT', str_replace(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])),'', str_replace('\\', '/',dirname(__FILE__))));
define('THEME', 'start');
define('FRAMEWORK', '');	// indicating framework integrating - not used yet**
define('CDN', false);        // use Cloud CDN by default. False to use the local libraries
define('DEBUG', false); // *** MUST SET TO FALSE WHEN DEPLOYED IN PRODUCTION *
define('UPLOADEXT', 'gif,png,jpg,jpeg');
define('UPLOADDIR', '/Applications/MAMP/localhost/phpGridx/examples/SampleImages/');



/******** DO NOT MODIFY ***********/
require_once('phpGrid.php');
/**********************************/