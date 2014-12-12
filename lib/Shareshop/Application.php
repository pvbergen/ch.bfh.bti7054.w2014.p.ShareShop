<?php
namespace Shareshop;

use Shareshop\Config;
use Shareshop\Request;
use Shareshop\View;
use Shareshop\Plugin\PluginManager;

class Application {
	
	/**
	 * Event just before controller and action are dispatched.
	 * 
	 * @var string
	 */
	const ROUTE_PREDISPATCH = "ROUTE.PREDISPATCH";

	/**
	 * Event just after controller and action have been dispatched.
	 *
	 * @var string
	 */
	const ROUTE_POSTDISPATCH = "ROUTE.POSTDISPATCH";
	
	/**
	 * 
	 */
	protected static $_instance;
	
	/**
	 * 
	 * @var \Shareshop\View
	 */
	protected $_view = null;
	
	/**
	 * 
	 * @var \Shareshop\Request
	 */
	protected $_request = null;
	
	/**
	 * 
	 * @var \Shareshop\Config
	 */
	protected static $_config = null;
	
	/**
	 * 
	 * @var \Shareshop\Plugin\PluginManager
	 */
	protected static $_pluginManager = null;
	
	/**
	 * Constructs a new Application object,
	 * parses request URI, determines requested route and
	 * initializes request and view object.
	 */	
	protected function __construct() 
	{		
		$bootstrapClass = Application::getConfig()->bootstrap;
		if (is_string($bootstrapClass)) {
			$bootstrap = new $bootstrapClass();
			$methods = get_class_methods($bootstrap);
			foreach ($methods as $method) {
				if (strpos($method, 'init') === 0) {
					$bootstrap->$method();
				}
			}
		}
		$defaultRoute = explode("\\", self::getConfig()->defaultRoute);
		$uriParts = explode('?', $_SERVER['REQUEST_URI']);
		$uriParts = explode('/', $uriParts[0]);
		array_shift($uriParts);
		if (!empty($uriParts[0])) {
			$controller = ucfirst(strtolower($uriParts[0]));
		} else {
			$controller = $defaultRoute[0];
		}
		
		if (!empty($uriParts[1])) {
			$action = strtolower($uriParts[1]);
		} else {
			$action = $defaultRoute[1];
		}
		
		$this->_view = new View();
		$this->_request = new Request($controller, $action);
		if ($this->_request->isAjaxRequest()) {
			$this->_view->renderAsAjax(true);
		}
	}
	
	/**
	 * Returns the application instance.
	 * 
	 * @return \Shareshop\Application
	 */
	public static function getInstance()
	{
		if (Application::$_instance == null) {
			Application::$_instance = new Application();
		}
		return Application::$_instance;
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
	
	/**
	 * Returns the application plugin manager.
	 *
	 * @return \Shareshop\Plugin\PluginManager
	 */
	public static function getPluginManager()
	{
		if (self::$_pluginManager == null) {
			self::$_pluginManager = new PluginManager();
		}
		return self::$_pluginManager;
	}
	
	/**
	 * Routes the request.
	 * Initialises controller with view and request and executes action, both corresponding to request.
	 * 
	 * @throws \Exception
	 */
	public function route()
	{		
		Application::getPluginManager()->inform(self::ROUTE_PREDISPATCH, array('request' => $this->_request));
		try {
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
			$errorControllerClass = Application::getConfig()->errorController;
			if (is_string($errorControllerClass)) {
				$this->_view = new View();
				$this->_view->setLayout('error');
				$controllerObj = new $errorControllerClass($e);
				$controllerObj->view = $this->_view;
				$controllerObj->request = $this->_request;
				$controllerObj->indexAction();
			} 
		}
		Application::getPluginManager()->inform(self::ROUTE_POSTDISPATCH);
	}
	
	public function forward()
	{
		header("Location: /" . $this->_request->getController()  . "/" . $this->_request->getAction());
	}
	
	protected function __clone()
	{
		
	}
}