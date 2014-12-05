<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Category;
use Application\Models\Db\Location;
use Application\Models\Db\SearchParameter;

class ArticleController extends \Shareshop\Controller {
	

	public function saveAction()
	{
		$post = $this->request->getPost();
		$files = $_FILES;

		$article = $this->insertArticle($post, $files);
		$this->view->register('article/show',  array('article' => $article));
		$this->view->render();
		
	}
	
	private function insertArticle($post, $files) {
		$location = Location::findById(1);
		$arr = $post['productCategory'];
		$resArray = array();
		foreach ($arr as $key => $val) {
			$resArray[$key] = Category::findById($val);
		}
		$fileName = basename($files['picture']['name']);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$fileContent;
		if($imageFileType == 'jpg' || $imageFileType == 'png' || $imageFileType == 'jpeg'
				|| $imageFileType != 'gif' ) {
				$fileContent = file_get_contents($files['picture']['tmp_name']);
		}
		$image = base64_encode($fileContent);
		$article = Article::create();
		$article->setDescription($post['productDescription']);
		$article->setName($post['productName']);
		$article->setCategories($resArray);
		$article->setLocation($location);
		$article->setImage($image);
		//$article->setImageType($imageFileType);
		$article->save();	
		return $article;	
	}
	
	public function searchAction() {
		$params = $this->request->getParameters();
		
		$searchParam1 = new SearchParameter('name', $params['search']);
		$searchParam2 = new SearchParameter('description', $params['search']);
		$paramArr = array( $searchParam1, $searchParam2 );
		$result = Article::searchForArticles($paramArr);	
		$articles = Article::loadArticles($result);
		$categories = $this->fetchFromArticles($articles);
		
		$final = $this->prepareCategoriesHirarchy($categories);
				
		$this->view->register('navigation/subnavigation', array('parentCategories' => $final[0], 'childCategories' => $final[1]), 'subnavigation');
		$this->view->register('article/list', array('articles' => $articles), 'content');
		$this->view->render();
	}
	
	public function getbycategoryAction() {
		$params = $this->request->getParameters();
		
		$searchParam1 = new SearchParameter('category', $params['category']);
		$paramArr = array( $searchParam1);
		$result = Article::searchForArticles($paramArr);
		$articles = Article::loadArticles($result);

		$this->view->register('article/list', array('articles' => $articles), 'content');
		$this->view->render();		
	}
	
	public function getimageAction() {
		$params = $this->request->getParameters();
		$article = Article::findById($params['id']);
		$image = base64_decode($article->getImage());
		$imageType = $article->getImageType();
		$imageOutType;
		if ($imageType == 'jpg' || $imageType == 'jpeg') {
			$imageOutType = 'jpeg';
		} else if('png') {
			$imageOutType = 'png';
		}
		header('Content-Type: image/' + $imageOutType);
		header('Content-Length: ' . strlen($image));
		echo $image;
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
		$categories = Category::findAll();
		$final = $this->prepareCategoriesHirarchy($categories);
		$this->view->register('navigation/subnavigation', array('parentCategories' => $final[0], 'childCategories' => $final[1]), 'subnavigation');
		$this->view->register('article/list', array('articles' => $articles));
		$this->view->render();
	}
	
	public function showAction()
	{
		$params = $this->request->getParameters();
		if (!isset($params['item']) || empty($params['item'])) {
			$this->view->redirect('index', 'index');
		}
		$article = Article::findById($params['item']);
		//$article = new Article($params['item'], substr(md5($params['item']), rand(0, 10), 10), md5($params['item']), md5($params['item']), md5($params['item']), md5($params['item']));
		$this->view->register('article/show', array('article' => $article));
		$this->view->render();
	}
	
	private function fetchFromArticles($articles) {
		$categories = array();
		foreach ($articles as $article) {
			$categories = array_merge($categories, $article->getCategories());
		}
		$categories = array_unique($categories);
		return $categories;
	}
	
	private function prepareCategoriesHirarchy($categories) {
		$result = array();
		$catParent = array();
		$catChild = array();
		$CCounter = 0;
		$PCounter = 0;
		foreach ($categories as $category) {
			if ($category->getParentId() == null) {
				$catParent[$PCounter] =  $category;
				$PCounter++;
			} else {
				$catChild[$CCounter] =  $category;
				$CCounter++;
			}
		}
		$result[0] = $catParent;
		$result[1] = $catChild;
		return $result;
	}
}