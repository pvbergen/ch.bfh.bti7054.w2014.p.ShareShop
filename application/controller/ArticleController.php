<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Category;
use Application\Models\Db\Location;
use Application\Models\Db\SearchParameter;
use Application\Plugin\Auth;
use Application\Models\Db\User;

class ArticleController extends \Shareshop\Controller {
	

	public function saveAction()
	{
		$post = $this->request->getPost();
		$files = $_FILES;

		$article = $this->insertArticle($post, $files);
		//$this->insertUserName();
		$this->view->register('article/show',  array('article' => $article));
		$this->view->render();
		
	}
	

	
	public function searchAction() {
		$params = $this->request->getParameters();
		
		$articles = array();
		$categories = array();
		
		if ($params['categorySearch'] && ($params['categorySearch'] === 'true')) {
			$categoryId = $params['category'];
			$articles = $this->getArticlesByCategory($categoryId);
			$category1 = Category::findById($categoryId);
			$category2 = Category::findById($category1->getParentId());
			$categories[0] = $category1;
			$categories[1] = $category2;
		} else {
			$searchParam1 = new SearchParameter('name', $params['search']);
			$searchParam2 = new SearchParameter('description', $params['search']);
			$paramArr = array( $searchParam1, $searchParam2 );
			$result = Article::searchForArticles($paramArr);
			$articles = Article::loadArticles($result);
			$categories = $this->fetchFromArticles($articles);			
		}


		
		$final = $this->prepareCategoriesHirarchy($categories);
		$this->insertUserName();
		$this->view->register('navigation/subnavigation', array('parentCategories' => $final[0], 'childCategories' => $final[1]), 'subnavigation');
		$this->view->register('article/list', array('articles' => $articles), 'content');
		$this->view->render();
	}
	
	public function getbycategoryAction() {
		$params = $this->request->getParameters();
		

		$catId = $params['category'];

		$articles = $this->getArticlesByCategory($catId);

		$this->view->register('article/list', array('articles' => $articles), 'content');
		$this->view->render();
	}
	

	
	
	public function uploadAction()
	{
		$preDefined = array();
		$preDefined['name'] = '';
		$preDefined['description'] = '';
		$preDefined['id'] = 'none';
		$categories = Category::findAllParents();
		$this->insertUserName();
		$this->view->register('article/upload', array('categories' => $categories, 'preDefined' => $preDefined));
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->render();
	}
	
	public function subcategoriesAction() {
		$params = $this->request->getParameters();
		$arr = explode('-', $params['id']);
		$categories = array();
		foreach ($arr as $catId) {
			$categories = $this->helpArrayMerge($categories, Category::findAllSubCategories($catId));
		}
		$this->view->register('article/subCategories', array('categories' => $categories));
		$this->view->render();
	}
	
	
	public function submitcategoryAction() {
		$params = $this->request->getParameters();
		$category = Category::create();
		$category->setName($params['subCategory']);
		$category->setParentId($params['id']);
		$category->save();
		$this->subcategoriesAction();
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
	
	public function userlistAction() {
		$articles = Article::findArticlesByUserId(Auth::getSession()->getUserId());
		$this->insertUserName();
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->register('article/list', array('articles' => $articles, 'isUserList' => true));
		$this->view->render();		
	}
	
	public function showAction()
	{
		$params = $this->request->getParameters();
		if (!isset($params['item']) || empty($params['item'])) {
			$this->view->redirect('index', 'index');
		}
		$article = Article::findById($params['item']);

		$this->view->register('article/show', array('article' => $article));
		$this->view->render();
	}
	
	public function deleteAction() {
		$params = $this->request->getParameters();
		$article = Article::findById($param['id']);
		
		if (Auth::getSession()->getUserId() !== $article->getUserId()) {
			trigger_error("Not your Item!", E_USER_ERROR);
		}
		
	}
	
	public function editAction() {
		$params = $this->request->getParameters();
		$article = Article::findById($params['id']);
		$userId = Auth::getSession()->getUserId();
		if ($userId !== $article->getUserId()) {
			trigger_error("Not your Item!", E_USER_ERROR);
		}
		$preDefined = array();
		$preDefined['name'] = $article->getName();
		$preDefined['description'] = $article->getDescription();
		$preDefined['id'] = $article->getId();
		$categories = Category::findAllParents();
		$this->insertUserName();
		$this->view->register('article/upload', array('categories' => $categories, 'preDefined' => $preDefined));
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->render();		
	}
	
	
	private function insertArticle($post, $files) {
		$arr = $post['productSubCategory'];
		$resArray = array();
		foreach ($arr as $key => $val) {
			$resArray[$key] = Category::findById($val);
		}
		chdir('../public/publicImgs');
		$fileName = basename($files['picture']['name']);
		$imageFileType = pathinfo($fileName,PATHINFO_EXTENSION);
		$fileName = "image." + $imageFileType;
		$fileURL;
		if($imageFileType == 'jpg' || $imageFileType == 'png' || $imageFileType == 'jpeg'
				|| $imageFileType != 'gif' ) {
			$fileURL = getcwd() . '\\' . $fileName;
			while (file_exists($fileURL)) {
				$rand = rand(1, 10000);
				$fileName = '_' . $rand . '_' . $fileName;
				$fileURL = getcwd() . '\\' . $fileName;
			}
			move_uploaded_file($files['picture']['tmp_name'], $fileURL);
		}
		$image = '/publicimgs/' . $fileName;
		$article = Article::create();
		$article->setDescription($post['productDescription']);
		$article->setName($post['productName']);
		$article->setCategories($resArray);
		$article->setUserId(Auth::getSession()->getUserId());
		$article->setImage($image);
		$article->save();
		return $article;
	}	
	
	private function getArticlesByCategory($catId) {
		$category = Category::findById($catId);
		$articles = array();
		if ($category->getParentId() == null) {
			$categories = Category::findAllSubCategories($catId);
			foreach ($categories as $cat) {
				$articles = $this->helpArrayMerge($articles, Article::findArticlesByCategoryId($cat->getId()));
			}
	
		} else {
			$articles = Article::findArticlesByCategoryId($catId);
		}
		
		return $articles;
	}	
	
	private function helpArrayMerge($arr1, $arr2) {
		$identifier = array();
		$result = array();
		$i = 0;
		foreach($arr1 as $el) {
			$identifier[$i++] = $el->getId();
		}
		$result = $arr1;
		foreach($arr2 as $el) {
			if (!(in_array($el->getId(), $identifier))) {
				$result[$i] = $el;
				$identifier[$i++] = $el->getId();
			}
		}
		return $result;
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
	
	private function insertUserName() {
		$user = User::findById(Auth::getSession()->getUserId());
		$this->view->register('auth/username', array('username' => $user->getUsername()), 'username');
	}
}