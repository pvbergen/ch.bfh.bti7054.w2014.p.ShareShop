<?php
namespace Shareshop;

class Controller {
	
	/**
	 * 
	 * @var View
	 */
	protected $_view = null;

	/**
	 * 
	 * @var Request
	 */
	protected $_request = null;

	public function __construct(View $view)
	{
		$this->_request = new Request();
		$this->_view = $view;
	}
	
}