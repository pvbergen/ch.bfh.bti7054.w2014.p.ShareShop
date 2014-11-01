<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Category;

class ArticleController extends \Shareshop\Controller {
	

	public function saveAction()
	{
		$post = $this->request->getPost();
		$article = Article::create();
		$article->setDescription($post['productDescription']);
		$article->setName($post['productName']);
		//$article->setLocationId(location);
		$article->setCategoryId(1);
		$article->save();
		$this->view->register('article/show',  array('post' => $post), 'navigation');
		$this->view->render();
	}
	
	public function uploadAction()
	{
		$categories = Category::readAll();
		$this->view->register('article/upload', array('categories' => $categories));
		//$this->view->setLayout('static');
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
		if (!isset($params['item']) || empty($params['item'])) {
			$this->view->redirect('index', 'index');
		}
		$article = new Article($params['item'], substr(md5($params['item']), rand(0, 10), 10), md5($params['item']), md5($params['item']), md5($params['item']), md5($params['item']));
		$this->view->register('index/detail', array('article' => $article));
		$this->view->render();
	}
}