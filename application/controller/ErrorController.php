<?php
namespace Application\Controller;

class ErrorController {
	
	public function indexAction() {
		$this->_view->register('error/index', array());
		$this->_view->render();
	}
	
}