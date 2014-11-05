<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;

class IndexController extends \Shareshop\Controller {
	
	public function indexAction()
	{
		//$db = DBAccess::getInstance();
		
		//print_r($db->getAllArticles());
// 		$articles = array();
// 		for($i = 0; $i < 50; $i++) {
// 			$articles[] = new Article($i, substr(md5($i), rand(0, 10), 10), md5($i), md5($i), md5($i), md5($i));
// 		}
		
		$this->view->register('index/index', array('articles' => $articles));
		$this->view->render();
	}
	
	public function listAction()
	{
		$this->view->register('index/index', array('teststring' => 'das ist eine liste'));
		$this->view->render();
	}
	
	public function detailAction()
	{
		$params = $this->request->getParameters();
		if (!isset($params['item']) || !is_numeric($params['item'])) {
			$this->view->redirect('index', 'index');
		}
		$article = new Article($params['item'], substr(md5($params['item']), rand(0, 10), 10), md5($params['item']), md5($params['item']), md5($params['item']), md5($params['item']));
		$this->view->register('index/detail', array('article' => $article));
		$this->view->render();
	}
}