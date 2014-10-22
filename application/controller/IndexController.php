<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Shareshop\Application;
class IndexController extends \Shareshop\Controller {
	
	public function indexAction()
	{
		//$db = new DBAccess();
			
		$this->view->register('index/index', array('teststring' => 'das ist ein test'));
		$this->view->render();
	}
	
	public function listAction()
	{
		$this->view->register('index/index', array('teststring' => 'das ist eine liste'));
		$this->view->render();
	}
	
	
	
}