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
	
	public function __construct() {
		$this->connect ();
	}
	
	
	private function connect() {
		try {
			$this->_conn = new \PDO ( 'mysql:host=' . $this->_HOST . ';dbname=' . $this->_DB, $this->_USER, $this->_PASS );
			$this->_conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch ( PDOException $e ) {
			echo $e->getMessage ();
		}
	}
	
	// ------------------------ Helpers ---------------------------- //
	
	private function createArticleFromDBRow($row) {
		return new Article($row->art_id, $row->art_name, $row->art_description, $row->art_image, $row->art_loc_id, $row->art_cat_id);
	}
	
	// ------------------------ Article ---------------------------- //
	public function getAllArticles() {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_articles' );
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			$stmt->execute();
			
			while ( $row = $stmt->fetch() ) {
				$articles [] = $this->createArticleFromDBRow($row);
			}
			return $articles;
			
		} catch ( PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function getArticleById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_articles WHERE art_id=:id' );
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			$stmt->bindParam ( ':id', $id );

			$stmt->execute();
		    $row = $stmt->fetch();
			
			$article=$this->createArticleFromDBRow($row);
			return $article;
			
		} catch ( PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	public function insertUpdateArticle($article) {
	}
	
	// ------------------------ Category ---------------------------- //
	public function getCategoryById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_categories WHERE cat_id=:id' );
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			$stmt->bindParam ( ':id', $id );

			$stmt->execute();
		    $row = $stmt->fetch();
			
			$category=new Category($row->cat_id, $row->cat_name, $row->cat_parent_id);
			
			return $category;
		} catch ( PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	// ------------------------ Location ---------------------------- //
	public function getLocationById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_locations WHERE loc_id=:id' );
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			$stmt->bindParam ( ':id', $id );

			$stmt->execute();
		    $row = $stmt->fetch();
			
			$location=new Location($row->loc_id, $row->loc_postcode);
			
			return $location;
		} catch ( PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}

}
?>