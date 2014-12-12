<?php
namespace Shareshop;

use Application\Hooks;
/**
 * Request provides methods to retrieve the HTTP request data (headers, get and post data).
 * 
 * @author Philippe von Bergen
 *
 */
class Request {
	
	/**
	 * The hooks of the application.
	 * 
	 * @var Hooks
	 */
	protected $_hooks = null;
	
	/**
	 * The parameters of the HTTP request.
	 * 
	 * @var \String[]
	 */
	protected $_parameters;
	
	/**
	 * The headers of the HTTP request.
	 * 
	 * @var \String[]
	 */
	protected $_headers;
	
	/**
	 * The controller name of the request.
	 * 
	 * @var string
	 */
	protected $_controller;
	
	/**
	 * The action name of the request.
	 * 
	 * @var string
	 */
	protected $_action;
	
	protected $_message = "";
	
	protected $_error = "";
	
	/**
	 * Creates a new request object.
	 */
	public function __construct($controller = 'index', $action = 'index')
	{
		$this->_controller = $controller;
		$this->_action = $action;
		
		$uriParts = explode('?', $_SERVER['REQUEST_URI']);
		$this->_parameters = explode('/', $uriParts[0]);
		array_shift($this->_parameters);
		array_shift($this->_parameters);
		array_shift($this->_parameters);
		for ($i=0; $i < count($this->_parameters); $i++) {
			if (isset($this->_parameters[$i+1])) {
				$this->_parameters[$this->_parameters[$i]] = $this->_parameters[$i+1];
				unset($this->_parameters[$i+1]);
			}
			unset($this->_parameters[$i]);
			$i++;
		}
		
		$this->_parameters = array_merge($this->_parameters, $_GET);
		$this->_headers = apache_request_headers();
	}
	
	/**
	 * Returns the parameter part of the path and get parameters as a key->value list.
	 * Actual GET parameters overwrite path parameters.
	 * 
	 * @return multitype:string The list of key value pairs. 
	 */
	public function getParameters()
	{
		return $this->_parameters;
	}
	
	/**
	 * Returns the POST data.
	 * 
	 * @return multitype:string
	 */
	public function getPost()
	{
		return $_POST;
	}
	
	/**
	 * Returns the request headers as a key->value list.
	 *
	 * @return multitype:string The list of key value pairs.
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}
	
	
	/**
	 * Checks, whether the current request was made with ajax.
	 * 
	 * @return boolean True, if current request was ajax, false otherwise.
	 */
	public function isAjaxRequest()
	{
		if (isset($this->_headers['X-Requested-With'])) {
			return $this->_headers['X-Requested-With'] == 'XMLHttpRequest';
		}
		return false;
	}
	
	/**
	 * Returns the controller name of the request.
	 * 
	 * @return string
	 */
	public function getController()
	{
		return $this->_controller;	
	}
	
	/**
	 * Returns the action name of the request.
	 * 
	 * @return string
	 */
	public function getAction()
	{
		return $this->_action;
	}

	public function getMessage()
	{
		return $this->_message;
	}
	
	public function getError()
	{
		return $this->_error;
	}
	

	public function setController($controller = 'Index')
	{
		$this->_controller = ucfirst(strtolower($controller));
	}
	
	public function setAction($action = 'index')
	{
		$this->_action = strtolower($action);
	}
	
	public function setMessage($message)
	{
		$this->_message = $message;
	}
	
	public function setError($error)
	{
		$this->_error = $error;
	}
	
	/**
	 * Dispatch the request.
	 * Initialises controller with view and request and executes action, both corresponding to request.
	 *
	 * @throws \Exception
	 */
	public function dispatch()
	{
		echo 'dispatch in request';
		try {
			$this->_hooks->preDispatch($this, $this->_view);
				
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
		$this->_hooks->postDispatch($this->_request, $this->_view);
	}
}