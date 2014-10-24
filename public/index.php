<?php
mb_internal_encoding('UTF-8');

use Shareshop\Application;
define("BASE_PATH", dirname(dirname(__FILE__)));
define("APPLICATION_PATH", BASE_PATH . '\application');
define("APPLICATION_ENV", getenv('APPLICATION_ENV'));

require_once BASE_PATH . '/lib/SplClassLoader.php';

$autoloader = new SplClassLoader('Shareshop', BASE_PATH . '/lib');
$autoloader->register();

$autoloader = new SplClassLoader('Application', BASE_PATH);
$autoloader->register();

$application = new Application();
$application->route();