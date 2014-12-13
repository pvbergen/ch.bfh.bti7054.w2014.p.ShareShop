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
		//$location = Location::findById(1);
		//print_r($post);
		$arr = $post['productSubCategory'];
		$resArray = array();
		foreach ($arr as $key => $val) {
			$resArray[$key] = Category::findById($val);
		}
		chdir('../public/publicImgs');
		$fileName = basename($files['picture']['name']);
		$imageFileType = pathinfo($fileName,PATHINFO_EXTENSION);
		$fileURL;
		if($imageFileType == 'jpg' || $imageFileType == 'png' || $imageFileType == 'jpeg'
				|| $imageFileType != 'gif' ) {
				$fileURL = getcwd() . '\\' . $fileName;
				//print_r($fileURL);
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
		$article->setUserId(1);
		$article->setImage($image);
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
		
// 		$searchParam1 = new SearchParameter('category', $params['category']);
// 		$paramArr = array( $searchParam1);
// 		$result = Article::searchForArticles($paramArr);
		$catId = $params['category'];
		$category = Category::findById($catId);
		$articles = array();
		if ($category->getParentId() == null) {
			$categories = Category::findAllSubCategories($catId);
			//print_r(count($categories));
			foreach ($categories as $cat) {
				$articles = $this->helpArrayMerge($articles, Article::findArticlesByCategoryId($catId));
				//print_r(count($articles));
			}			
			
		} else {
			$articles = Article::findArticlesByCategoryId($catId);
		}
		
		//Article::loadArticles($result);
		
		$this->view->register('article/list', array('articles' => $articles), 'content');
		$this->view->render();
	}
	
// 	public function getimageAction() {
// 		$params = $this->request->getParameters();
// 		$article = Article::findById($params['id']);

// 	}
	
	
	public function uploadAction()
	{
		$categories = Category::findAllParents();
		$this->view->register('article/upload', array('categories' => $categories));
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
	
	private function helpArrayMerge($arr1, $arr2) {
		$identifier = array();
		$result = array();
		$i = 0;
		print_r(count($arr2));
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