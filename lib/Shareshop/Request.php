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
	
	/**
	 * 
	 */
	public function __construct()
	{
		$uriParts = explode('?', $_SERVER['REQUEST_URI']);
		$this->_parameters = explode('/', $uriParts[0]);
		array_shift($this->_parameters);
		array_shift($this->_parameters);
		array_shift($this->_parameters);
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
}