<?php
use Shareshop\Request;
use Shareshop\View;
define("BASE_PATH", dirname(dirname(__FILE__)));
define("APPLICATION_PATH", BASE_PATH . '\application');

require_once BASE_PATH . '/lib/SplClassLoader.php';

$autoloader = new SplClassLoader('Shareshop', BASE_PATH . '/lib');
$autoloader->register();

$autoloader = new SplClassLoader('Application', BASE_PATH);
$autoloader->register();

$uriParts = explode('?', $_SERVER['REQUEST_URI']);
$uriParts = explode('/', $uriParts[0]);
array_shift($uriParts);

$controllerPrefix = ucfirst(strtolower($uriParts[0]));
$controllerFile = APPLICATION_PATH . '/controller/' . $controllerPrefix . 'Controller.php';
if (!file_exists($controllerFile)) {
	die('missing controller, implement error handling');
}
$controllerName = "\Application\Controller\\" . $controllerPrefix . "Controller";
$controller = new $controllerName(new View(APPLICATION_PATH));

$actionPrefix = strtolower($uriParts[1]);
if (empty($actionPrefix)) {
	$actionPrefix = 'index';
}
call_user_func(array($controller, $actionPrefix . "Action"));