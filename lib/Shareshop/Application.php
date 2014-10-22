<?php
namespace Shareshop;

use Application\Controller\ErrorController;
use Application\Bootstrap;
class Application {
	
	protected $_controller = 'index';
	protected $_action = 'index';
	protected $_bootstrap = null;
	protected static $_config = null;
	
	public function __construct() {
		$this->_bootstrap = new Bootstrap();
	}
	
	/**
	 * Returns the application configuration (as set in application.ini) as an object.
	 * Access any property as $config->key1->key2->key3 (equals key1.key2.key3 in the ini file).
	 * 
	 * @return \Shareshop\Config
	 */
	public static function getConfig()
	{
		if (self::$_config == null) {
			self::$_config = new Config(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		}
		return self::$_config;
	}
	
	public function route()
	{
		$uriParts = explode('?', $_SERVER['REQUEST_URI']);
		$uriParts = explode('/', $uriParts[0]);
		array_shift($uriParts);
		if (!empty($uriParts[0])) {
			$controller = ucfirst(strtolower($uriParts[0]));
		} else {
			$controller = 'Index';
		}
		
		if (!empty($uriParts[1])) {
			$action = strtolower($uriParts[1]);
		} else {
			$action = 'index';
		}
		
		$view = new View();
		$request = new Request($controller, $action);
		try {	
			$this->_bootstrap->preDispatch($request, $view);
			
			$controllerFile = APPLICATION_PATH . '/controller/' . $request->getController() . 'Controller.php';
			if (!file_exists($controllerFile)) {
				throw new \Exception('Missing controller file');
			}
			$controllerName = "\Application\Controller\\" . $request->getController() . "Controller";
			$controllerObj = new $controllerName();
			$controllerObj->view = $view;
			$controllerObj->request = $request;
						
			if (!method_exists($controllerObj, $request->getAction() . "Action")) {
				throw new \Exception('Missing action method');
			}
			call_user_func(array($controllerObj, $request->getAction() . "Action"));
			
		} catch(\Exception $e) {
			$view = new View();
			$view->setLayout('error');
			$controllerObj = new ErrorController($e);
			$controllerObj->view = $view;
			$controllerObj->request = $request;
			$controllerObj->indexAction(); 
		}
		$this->_bootstrap->postDispatch($request, $view);
	}
}