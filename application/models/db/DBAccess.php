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
	
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	private function __construct() {
		try {
			$this->_conn = new \PDO ( 'mysql:host=' . $this->_HOST . ';dbname=' . $this->_DB, $this->_USER, $this->_PASS );
			$this->_conn->setAttribute ( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			echo 'connection established.';
		} catch ( \PDOException $e ) {
			echo $e->getMessage ();
		}
	}

	// ------------------------ Helpers ---------------------------- //
	
	private function createArticleFromDatabaseRow($row) {
		return new Article($row->art_id, $row->art_name, $row->art_description, $row->art_image, $row->art_loc_id, $row->art_cat_id);
	}
	
	private function createCategoryFromDatabaseRow($row) {
		return new Category($row->cat_id, $row->cat_name, $row->cat_parent_id);
	}
	
	private function createLocationFromDatabaseRow($row) {
		return new Location($row->loc_id, $row->loc_postcode);
	}
	
	// ------------------------ Article ---------------------------- //
	public function readArticleById($id) {
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
		$stmt->execute(array(
				':id' 			=> 	$article->getId(),
				':name' 		=> 	$article->getName(),
				':description'  => 	$article->getDescription(),
				':image'        => 	$article->getImage(),
				':locationId'   => 	$article->getLocationId(),
				':categoryId'   => 	$article->getCategoryId(),
		));
		
		# Affected Rows?
		echo $stmt->rowCount(); // 1
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function readAllArticles() {
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
	public function readCategoryById($id) {
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
	
	public function readAllCategories(){
	}
	
	// ------------------------ Location ---------------------------- //
	public function readLocationById($id) {
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
	
	public function readAllLocations() {
	}
}
?>