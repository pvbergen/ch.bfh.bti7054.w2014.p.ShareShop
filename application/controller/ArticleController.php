<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Category;
use Application\Models\Db\Location;
use Application\Models\Db\SearchParameter;
use Application\Plugin\Auth;
use Application\Models\Db\User;
use Application\Models\Db\Exchange;

class ArticleController extends \Shareshop\Controller {
	

	public function saveAction()
	{
		$post = $this->request->getPost();
		$files = $_FILES;
		$get = $this->request->getParameters();
		if ($get['id'] !== 'none') {
			$article = $this->modifyArticle($get['id'], $post, $files);
		} else {
			$article = $this->insertArticle($post, $files);
		}	

		$this->insertUserName();
		$user = User::findById($article->getUserId());
		$location = Location::findById($user->getLocId());
		$this->view->register('article/show', array('article' => $article, 'location' => $location, 'user' => $user));
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->render();
		
	}
	
	public function searchAction() {
		$this->insertUserName();
		$params = $this->request->getParameters();
		
		$articles = array();
		$categories = array();
		$final = array();
		
		if (array_key_exists('categorySearch',$params) && ($params['categorySearch'] === 'true')) {
			$categoryId = $params['category'];
			$articles = $this->getArticlesByCategory($categoryId);
			$category1[0] = Category::findById($categoryId);
			$category2[1] = Category::findById($category1[0]->getParentId());
			$final[0] = $category2;
			$final[1] = $category1;
		} else {
			$searchParam1 = new SearchParameter('name', $params['search']);
			$searchParam2 = new SearchParameter('description', $params['search']);
			$paramArr = array( $searchParam1, $searchParam2 );
			$articles = Article::searchForArticles($paramArr);
			$categories = $this->fetchFromArticles($articles);	
			$catParents = array();
			$index = 0;
			foreach($categories as $cat) {
				$arrToMerge[0] = Category::findById($cat->getParentId());
				$catParents = $this->helpArrayMerge($catParents, $arrToMerge);
			}
			$final[0] = $catParents;
			$final[1] = $categories;
		}

		//$final = $this->prepareCategoriesHirarchy($categories);
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
		$preDefined['isEdit'] = false;
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
		$this->insertUserName();
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
		$user = User::findById($article->getUserId());
		$location = Location::findById($user->getLocId());
		
		$showExchange = false;
		if (Auth::getSession() != null) {
			if ($user->getId() != User::findBySessionId(Auth::getSession()->getId())->getId()) {
				$showExchange = true;
			}
		}

		$data = array('article' => $article, 'location' => $location, 'user' => $user, 'exchange' => $showExchange);
		
		if ($showExchange) {
			$exchanges = Exchange::findByUser($user);
			$rating = 0;
			$duration = 0;
			$countRatings = 0;
			$countDurations = 0;
			foreach ($exchanges as $exchange) {
				if ($user->getId() == $exchange->getAnsweringUser()->getId() && $exchange->getRequestingRating() > 0) {
					$rating += $exchange->getRequestingRating();
					$countRatings += 1;
				}
				if ($user->getId() == $exchange->getRequestingUser()->getId() && $exchange->getAnsweringRating() > 0) {
					$rating += $exchange->getRequestingRating();
					$countRatings += 1;
				}
				$previous = 0;
				foreach ($exchange->getSteps() as $step) {
					if ($previous != 0) {
						//if ($user->getId() == $exchange->getRequestingUser()->getId() && $step->getType() == )
						$gap = $step->getCreated() - $previous;	
					}
					$previous = $step->getCreated();
				}
			}
			if ($rating > 0) {
				$data['ratingData']['numeric'] = $rating/$countRatings;
			}
			
// 			if ($avgDuration > 0) {
// 				$data['ratingData']['avgDuration'] = $rating/$countRatings;
// 			}
		}
		
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->register('article/show', $data);
		$this->view->render();
	}
	
	public function deleteAction() {
		$params = $this->request->getParameters();
		$article = Article::findById($params['id']);
		
		if (Auth::getSession()->getUserId() !== $article->getUserId()) {
			trigger_error("Not your Item!", E_USER_ERROR);
		}
		$article->delete();
		$this->userlistAction();
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
		$preDefined['isEdit'] = true;
		$preDefined['categories'] = $article->getCategories();
		$categories = Category::findAllParents();
		$this->insertUserName();
		$this->view->register('article/upload', array('categories' => $categories, 'preDefined' => $preDefined));
		$this->view->register('navigation/staticSubnavigation', null, 'subnavigation');
		$this->view->render();		
	}
	
	public function plzsearchAction() {
		$params = $this->request->getParameters();
		$plz = $params['search'];
		$articles = array();
		$categories = array();
		$final = array();
		$users = array();
		
		$result = Location::findByPostCode($plz);
		$index = 0;
		foreach($result as $loc) {
			$users[$index++] = User::findByLocId($loc->getId());
		}		
		foreach($users as $user) {
			if (isset($user)) $articles = $this->helpArrayMerge($articles, Article::findArticlesByUserId($user->getId()));
		}	

		$categories = $this->fetchFromArticles($articles);
		$catParents = array();
		$index = 0;
		foreach($categories as $cat) {
			$arrToMerge[0] = Category::findById($cat->getParentId());
			$catParents = $this->helpArrayMerge($catParents, $arrToMerge);
		}
		$final[0] = $catParents;
		$final[1] = $categories;

		
		//$final = $this->prepareCategoriesHirarchy($categories);
		$this->insertUserName();
		$this->view->register('navigation/subnavigation', array('parentCategories' => $final[0], 'childCategories' => $final[1]), 'subnavigation');
		$this->view->register('article/list', array('articles' => $articles), 'content');
		$this->view->render();		
	}
	
	
	public function nearbysearchAction() {
		$params = $this->request->getParameters();
		$lng = $params['lng'];
		$lat = $params['lat'];
		$articles = array();
		$categories = array();
		$final = array();
		$users = array();
		
		$result = Location::findNearBy($lng, $lat);
		$index = 0;
		foreach($result as $loc) {
			$users[$index++] = User::findByLocId($loc->getId());
		}
		foreach($users as $user) {
			$articles = $this->helpArrayMerge($articles, Article::findArticlesByUserId($user->getId()));
		}
		
		$categories = $this->fetchFromArticles($articles);
		$catParents = array();
		$index = 0;
		foreach($categories as $cat) {
			$arrToMerge[0] = Category::findById($cat->getParentId());
			$catParents = $this->helpArrayMerge($catParents, $arrToMerge);
		}
		$final[0] = $catParents;
		$final[1] = $categories;
		
		
		//$final = $this->prepareCategoriesHirarchy($categories);
		$this->insertUserName();
		$this->view->register('navigation/subnavigation', array('parentCategories' => $final[0], 'childCategories' => $final[1]), 'subnavigation');
		$this->view->register('article/list', array('articles' => $articles), 'content');
		$this->view->render();
		
	}
	
	
	private function insertArticle($post, $files) {
		$arr = $post['productSubCategory'];
		$resArray = array();
		foreach ($arr as $key => $val) {
			$resArray[$key] = Category::findById($val);
		}
		if (array_key_exists('picture', $files)) {
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
		}
		$article = Article::create();
		$article->setDescription($post['productDescription']);
		$article->setName($post['productName']);
		$article->setCategories($resArray);
		$article->setUserId(Auth::getSession()->getUserId());
		$article->setImage($image);
		$article->save();
		return $article;
	}	

	private function modifyArticle($id, $post, $files) {
		$article = Article::findById($id);
		if (array_key_exists('productSubCategory', $post)) {
			$arr = $post['productSubCategory'];
			$resArray = array();
			foreach ($arr as $key => $val) {
				$resArray[$key] = Category::findById($val);
			}
			$article->setCategories($resArray);
		}	
		if ($files['picture']['size'] !== 0) {
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
			$article->setImage($image);
		}
		$article->setDescription($post['productDescription']);
		$article->setName($post['productName']);
		$article->setUserId(Auth::getSession()->getUserId());
		$article->modify();
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
			$categories = $this->helpArrayMerge($categories, $article->getCategories());
		}
		//$categories = array_unique($categories);
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
		if (Auth::getSession() != null) {
			$user = User::findById(Auth::getSession()->getUserId());
			if ($user != null) {
				$this->view->register('auth/username', array('username' => $user->getUsername()), 'username');
			}	
		}
	}
}