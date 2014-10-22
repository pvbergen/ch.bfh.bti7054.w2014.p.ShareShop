<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article2;

class IndexController extends \Shareshop\Controller {
	
	public function indexAction()
	{
		$db = new DBAccess();
		
		//print_r($db->getAllArticles());
		$articles = array();
		for($i = 0; $i < 50; $i++) {
			$articles[] = new Article2($i, substr(md5($i), rand(0, 10), 10), md5($i), md5($i), md5($i), md5($i));
		}
		
		
		$this->view->register('index/index', array('articles' => $articles));
		$this->view->render();
	}
	
	public function listAction()
	{
		$this->view->register('index/index', array('teststring' => 'das ist eine liste'));
		$this->view->render();
	}
}