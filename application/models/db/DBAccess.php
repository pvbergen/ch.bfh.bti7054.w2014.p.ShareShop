<?php
namespace Application\Models\Db;

use Application\Models\Db\Article;
use Application\Models\Db\Category;
use Application\Models\Db\Location;

/**
 * ****************************************************************************
 * Database access class - performs all database actions
 * ****************************************************************************
 */
class DBAccess {
	protected $_conn;
	protected $_HOST = 'localhost';
	protected $_DB = 'shareshop';
	protected $_USER = 'root';
	protected $_PASS = '';
	protected static $_instance = null;
	
	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self ();
		}
		return self::$_instance;
	}
	
	private function __construct() {
		try {
			$this->_conn = new \PDO ( 'mysql:host=' . $this->_HOST . ';dbname=' . $this->_DB, $this->_USER, $this->_PASS );
			$this->_conn->setAttribute ( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			$this->_conn->exec("set names utf8");
		} catch ( \PDOException $e ) {
			echo $e->getMessage ();
		}
	}

	// ------------------------ Helpers ---------------------------- //
	
	private function createArticleFromDatabaseRow($row) {
		$article = Article::create()
		->setId($row->art_id)
		->setName($row->art_name)
		->setDescription($row->art_description)
		->setImage($row->art_image)
		->setLocation($this->findLocationById($row->art_loc_id));
		
		$catId = $row->art_cat_id;
		while ($catId != null ) {
			$category =  $this->findCategoryById($catId);
			$categories [] = $category;
			$catId = $category->getParentId();
		}
		return $article;
	}
	
	private function createCategoryFromDatabaseRow($row) {
		$category = Category::create()
		->setId($row->cat_id)
		->setName($row->cat_name)
		->setParentId($row->cat_parent_id);
		return $category;
	}
	
	private function createLocationFromDatabaseRow($row) {
		$location = Location::create()
		->setId($row->loc_id)
		->setPostcode($row->loc_postcode);
		return $location;
	}
	
	// ------------------------ Article ---------------------------- //
	public function findArticleById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_articles WHERE art_id=:id' );
			$stmt->setFetchMode(\PDO::FETCH_OBJ);
			$stmt->bindParam ( ':id', $id );

			$stmt->execute();
		    $row = $stmt->fetch();
			
			$article=$this->createArticleFromDatabaseRow($row);
			return $article;
			
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function saveArticle($article) {
		try {
		$stmt = $this->_conn->prepare('INSERT INTO sha_articles VALUES(:id, :name, :description, :image, :locationId, :categoryId, null)');
		
		$mostSpecificCategoryId=null;
		foreach ( $article->getCategories() as $cat ) {
			if ($cat->getParentId() == $mostSpecificCategoryId || $mostSpecificCategoryId == null) {
				$mostSpecificCategoryId = $cat->getId();		
			}
		}
		
		$stmt->bindParam ( ':id', $article->getId());
		$stmt->bindParam ( ':name', $article->getName());
		$stmt->bindParam ( ':description', $article->getDescription());
		$stmt->bindParam ( ':image', $article->getImage());
		$stmt->bindParam ( ':locationId', $article->getLocation()->getId());
		$stmt->bindParam ( ':categoryId', $mostSpecificCategoryId);

		$stmt->execute();
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findAllArticles() {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_articles' );
			$stmt->setFetchMode(\PDO::FETCH_OBJ);
			$stmt->execute();
				
			while ( $row = $stmt->fetch() ) {
				$articles [] = $this->createArticleFromDatabaseRow($row);
			}
			return $articles;
				
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	// ------------------------ Category ---------------------------- //
	public function findCategoryById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_categories WHERE cat_id=:id' );
			$stmt->setFetchMode(\PDO::FETCH_OBJ);
			$stmt->bindParam ( ':id', $id );

			$stmt->execute();
		    $row = $stmt->fetch();
			
			$category=$this->createCategoryFromDatabaseRow($row);
			return $category;
			
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function saveCategory($category) {
		try {
			$stmt = $this->_conn->prepare('INSERT INTO sha_categories VALUES(:id, :name, :parentId)');
			$stmt->execute(array(
					':id' 			=> 	$category->getId(),
					':name' 		=> 	$category->getName(),
					':parentId'  => 	$category->getParentId()
			));
		
			# Affected Rows?
			echo $stmt->rowCount(); // 1
		} catch ( \PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findAllCategories(){
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_categories' );
			$stmt->setFetchMode(\PDO::FETCH_OBJ);
			$stmt->execute();
		
			while ( $row = $stmt->fetch() ) {
				$categories [] = $this->createCategoryFromDatabaseRow($row);
			}
			return $categories;
		
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	// ------------------------ Location ---------------------------- //
	public function findLocationById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_locations WHERE loc_id=:id' );
			$stmt->setFetchMode(\PDO::FETCH_OBJ);
			$stmt->bindParam ( ':id', $id );

			$stmt->execute();
		    $row = $stmt->fetch();
			
			$location=$this->createLocationFromDatabaseRow($row);
			return $location;
			
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function saveLocation($location) {
		try {
			$stmt = $this->_conn->prepare('INSERT INTO sha_locations VALUES(:id, :postcode)');
			$stmt->execute(array(
					':id' 			=> 	$location->getId(),
					':postcode' 	=> 	$location->getPostcode(),
			));
		
			# Affected Rows?
			echo $stmt->rowCount(); // 1
		} catch ( \PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findAllLocations() {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_locations' );
			$stmt->setFetchMode(\PDO::FETCH_OBJ);
			$stmt->execute();
		
			while ( $row = $stmt->fetch() ) {
				$locations [] = $this->createLocationFromDatabaseRow($row);
			}
			return $locations;
		
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
}
?>