<?php
namespace Application\Controller;

class IndexController extends \Shareshop\Controller {
	
	public function indexAction()
	{
		$this->view->register('index/index', array('teststring' => 'das ist ein test'));
		$this->view->render();
	}
	
	public function listAction()
	{
		$this->view->register('index/index', array('teststring' => 'das ist eine liste'));
		$this->view->render();
	}
	
	
	
}