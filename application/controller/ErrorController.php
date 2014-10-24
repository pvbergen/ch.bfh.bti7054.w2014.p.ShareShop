<?php
namespace Application\Controller;

use Shareshop\Controller;
class ErrorController extends Controller {
	
	protected $_e = null;
	
	public function __construct(\Exception $e) {
		$this->_e = $e;
	}
	
	public function indexAction() {
		$this->view->register('error/index', array('exception' => $this->_e, 'backtrace' => debug_backtrace()));
		$this->view->render();
	}
	
}