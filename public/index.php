<?php
use Shareshop\Application;
define("BASE_PATH", dirname(dirname(__FILE__)));
define("APPLICATION_PATH", BASE_PATH . '\application');

require_once BASE_PATH . '/lib/SplClassLoader.php';

$autoloader = new SplClassLoader('Shareshop', BASE_PATH . '/lib');
$autoloader->register();

$autoloader = new SplClassLoader('Application', BASE_PATH);
$autoloader->register();

$application = new Application();
$application->route();