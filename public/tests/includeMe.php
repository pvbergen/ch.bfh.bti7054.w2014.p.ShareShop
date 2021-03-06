<?php
mb_internal_encoding('UTF-8');
define("BASE_PATH", dirname(dirname(dirname(__FILE__))));
define("APPLICATION_PATH", BASE_PATH . '\application');
define("APPLICATION_ENV", 'development');

require_once BASE_PATH . '\lib\SplClassLoader.php';

$autoloader = new SplClassLoader('Shareshop', BASE_PATH . '\lib');
$autoloader->register();

$autoloader = new SplClassLoader('Application', BASE_PATH);
$autoloader->register();