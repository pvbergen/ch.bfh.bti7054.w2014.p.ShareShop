<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Category;
use Application\Models\Db\Location;

class ArticleController extends \Shareshop\Controller {
	

	public function saveAction()
	{
		$post = $this->request->getPost();
		$location = Location::findById(1);
		$arr = $post['productCategory'];
		$resArray = array();
		foreach ($arr as $key => $val) {
			$resArray[$key] = Category::findById($val);
		}
		$article = Article::create();
		$article->setDescription($post['productDescription']);
		$article->setName($post['productName']);
		$article->setCategories($resArray);
		$article->setLocation($location);
		$article->save();
		$this->view->register('article/show',  array('article' => $article));
		$this->view->render();
		
	}
	
	public function uploadAction()
	{
		$categories = Category::findAll();
		$this->view->register('article/upload', array('categories' => $categories));
		//$this->view->setLayout('static');
		$this->view->render();
	}
	
	public function listAction()
	{
			
		$articles = Article::findAll();
		$this->view->register('article/list', array('articles' => $articles));
		$this->view->render();
	}
	
	public function showAction()
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