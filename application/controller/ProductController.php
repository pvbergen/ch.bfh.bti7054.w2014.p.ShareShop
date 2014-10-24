<?php
namespace Application\Controller;

class ProductController extends \Shareshop\Controller {
	
	public function indexAction()
	{
		// Übersicht eigene Produkte
	}
	
	public function formAction()
	{
		// Formular für neues Produkt oder bestehendes mit ID
	}
	
	public function createAction()
	{
		if (is_Post) {
			if (has_id) {
			}	
		} else {
			// error
		}
		
		$this->_view->register('index/index', array('teststring' => 'das ist eine liste'));
		$this->_view->render();
		
		$params = $this->_request->getParameters();
		print_r($params);
	}
	
	public function catAction()
	{
		return 'json';
	}
}