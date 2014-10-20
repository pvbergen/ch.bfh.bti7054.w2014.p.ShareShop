<?php
namespace Shareshop;

class Application {
	
	protected $_controller = 'index';
	protected $_action = 'index';
	
	
	public function __construct() {
	
	}
	
	public function route()
	{
		try {
			
			$uriParts = explode('?', $_SERVER['REQUEST_URI']);
			$uriParts = explode('/', $uriParts[0]);
			array_shift($uriParts);
			if (!empty($uriParts[0])) {
				$this->_controller = ucfirst(strtolower($uriParts[0]));
			}
			$controllerFile = APPLICATION_PATH . '/controller/' . $this->_controller . 'Controller.php';
			if (!file_exists($controllerFile)) {
				throw new \Exception('Missing controller file');
			}
			$controllerName = "\Application\Controller\\" . $this->_controller . "Controller";
			$controller = new $controllerName(new View(APPLICATION_PATH));
			if (!empty($uriParts[1])) {
				$this->_action = strtolower($uriParts[1]);
			}
			if (!method_exists($controller, $this->_action . "Action")) {
				throw new \Exception('Missing action');
			}
			
			call_user_func(array($controller, $this->_action . "Action"));
				
		} catch(\Exception $e) {
			$controller = new \ErrorController();
			$controller->indexAction(); 
		}
	}
}