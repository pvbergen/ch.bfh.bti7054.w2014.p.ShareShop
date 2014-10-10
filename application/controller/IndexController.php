<?php
namespace Application\Controller;

class IndexController extends \Shareshop\Controller {
	
	public function indexAction()
	{
		$this->_view->register('index/index', array('teststring' => 'das ist ein test'));
		$this->_view->render();
	}
	
	public function listAction()
	{
		$this->_view->register('index/index', array('teststring' => 'das ist eine liste'));
		$this->_view->render();
		
		$params = $this->_request->getParameters();
		print_r($params);
	}
	
	
	
}