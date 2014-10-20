<?php
namespace Shareshop;

class Request {
	
	/**
	 * 
	 * @var \String[]
	 */
	protected $_parameters;
	
	/**
	 * 
	 * @var \String[]
	 */
	protected $_headers;
	
	protected $_controller;
	protected $_action;
	
	/**
	 * 
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
	 * Returns the path and get parameters as a key->value list.
	 * 
	 * @return multitype:String The list of key value pairs. 
	 */
	public function getParameters()
	{
		return $this->_parameters;
	}
	
	public function getPost()
	{
		return $_POST;
	}
	
	/**
	 * Returns the request headers as a key->value list.
	 *
	 * @return multitype:String The list of key value pairs.
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
		return $this->_headers['X-Requested-With'] == 'XMLHttpRequest';
	}
	
	public function getController()
	{
		return $this->_controller;	
	}
	
	public function getAction()
	{
		return $this->_action;
	}
	

	public function setController($controller = 'Index')
	{
		$this->_controller = ucfirst(strtolower($controller));
	}
	
	public function setAction($action = 'index')
	{
		$this->_action = strtolower($action);
	}
}