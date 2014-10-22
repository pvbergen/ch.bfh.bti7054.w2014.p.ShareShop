<?php
namespace Shareshop;

use Application\Controller\ErrorController;
use Application\Bootstrap;
class Application {
	
	protected $_bootstrap = null;
	protected $_view = null;
	protected $_request = null;
	
	protected static $_config = null;
	
	public function __construct() {
		$this->_bootstrap = new Bootstrap();
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
		
		$this->_view = new View();
		$this->_request = new Request($controller, $action);
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
		try {	
			$this->_bootstrap->preDispatch($this->_request, $this->_view);
			
			$controllerFile = APPLICATION_PATH . '/controller/' . $this->_request->getController() . 'Controller.php';
			if (!file_exists($controllerFile)) {
				throw new \Exception('Missing controller file');
			}
			$controllerName = "\Application\Controller\\" . $this->_request->getController() . "Controller";
			$controllerObj = new $controllerName();
			$controllerObj->view = $this->_view;
			$controllerObj->request = $this->_request;
						
			if (!method_exists($controllerObj, $this->_request->getAction() . "Action")) {
				throw new \Exception('Missing action method');
			}
			call_user_func(array($controllerObj, $this->_request->getAction() . "Action"));
			
		} catch(\Exception $e) {
			$this->_view = new View();
			$this->_view->setLayout('error');
			$controllerObj = new ErrorController($e);
			$controllerObj->view = $this->_view;
			$controllerObj->request = $this->_request;
			$controllerObj->indexAction(); 
		}
		$this->_bootstrap->postDispatch($this->_request, $this->_view);
	}
}