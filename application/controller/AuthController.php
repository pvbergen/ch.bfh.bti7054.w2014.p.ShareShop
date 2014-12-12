<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Shareshop\Application;

class AuthController extends \Shareshop\Controller {
	
	public function indexAction()
	{
		$this->view->register('auth/subnavigation', array(), 'subnavigation');
		$this->view->register('auth/index', array('error' => $this->request->getError()));
		$this->view->render();
	}
	
	public function loginAction()
	{
		$this->request->setController('Article');
		$this->request->setAction('list');
		Application::getInstance()->forward();
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