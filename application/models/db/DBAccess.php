<?php

namespace Application\Models\Db;

use Application\Models\Db\Article;
use Application\Models\Db\Category;
use Application\Models\Db\Location;
use Application\Models\Db\User;
use Shareshop\Application;

/**
 * ****************************************************************************
 * Database access class - performs all database actions.
 * ****************************************************************************
 */
class DBAccess {
	protected $_conn;
	protected static $_instance = null;
	
	
	/**
	 * 
	 * @return \Application\Models\Db\DBAccess
	 */
	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self ();
		}
		return self::$_instance;
	}
	
	private function __construct() {
		$config = Application::getConfig ();
		try {
			$this->_conn = new \PDO ( 'mysql:host=' . $config->db->host . ';dbname=' . $config->db->database, $config->db->user, $config->db->password );
			$this->_conn->setAttribute ( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		} catch ( \PDOException $e ) {
			echo $e->getMessage ();
		}
	}

	// ------------------------ Helpers ---------------------------- //
	private function createArticleFromDatabaseRow($row) {
		$article = Article::create ()->setId ( $row->art_id )->setName ( $row->art_name )->setDescription ( $row->art_description )->setImage ( $row->art_image )->setUserId( $row->art_usr_id );
		
// 		$catId = $row->art_cat_id;
// 		while ( $catId != null ) {
// 			$category = $this->findCategoryById ( $catId );
// 			$categories [] = $category;
// 			$catId = $category->getParentId ();
// 		}
// 		$article->setCategories($categories);
		
		return $article;
	}
	
	private function createCategoryFromDatabaseRow($row) {
		$category = Category::create ()->setId ( $row->cat_id )->setName ( $row->cat_name )->setParentId ( $row->cat_parent_id );
		return $category;
	}
	
	private function createLocationFromDatabaseRow($row) {
		$location = Location::create ()->setId ( $row->loc_id )->setPostcode ( $row->loc_postcode );
		return $location;
	}
	
	// ------------------------ Article ---------------------------- //
	public function loadArticles($arrArticleIds) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_articles WHERE art_id IN (:ids)' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':ids', implode(',', $arrArticleIds) );
			$stmt->execute ();
			
			$articles = array();
			while ( $row = $stmt->fetch () ) {
				$articles [] = $this->createArticleFromDatabaseRow ($row);
			}
			return $articles;
			
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function searchForArticles($arrSearchParams) {
		try {
			$paramBindings=array();
			$queryString='';
			foreach ( $arrSearchParams as $param ) {
				$queryString .= 'art_' . $param->getField() . ' LIKE :' . $param->getField();
				$queryString .= ' OR ';
				$paramBindings[':' . $param->getField()] = '%' . $param->getSearchString() . '%';
			}
			$queryString = substr($queryString, 0,-3);
	
			$stmt = $this->_conn->prepare( 'SELECT * FROM sha_articles WHERE ' . $queryString );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->execute ($paramBindings);
			
			$articleIds = array();
			$index = 0;
			while ( $row = $stmt->fetch () ) {
				$articleIds[$index] = $row->art_id;
				$index++;
			}

			return $articleIds;
			
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findArticleById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_articles WHERE art_id=:id' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
			
			$stmt->execute ();
			$row = $stmt->fetch ();
			
			if ($row != null) {
				return $this->createArticleFromDatabaseRow ( $row );
			}
			
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findArticlesByCategoryId($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_art_cat_rel WHERE cat_id=:id' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
				
			$stmt->execute ();
			
			$articles = array();
			$index = 0;
			while ( $row = $stmt->fetch () ) {
				$articles[$index++] = $this->findArticleById($row->art_id);
			}			
			return $articles;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}		
	}

	public function findArticlesByUserId($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_articles WHERE art_usr_id=:id' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
	
			$stmt->execute ();
				
			$articles = array();
			while ( $row = $stmt->fetch () ) {
				$articles [] = $this->createArticleFromDatabaseRow ($row);
			}
			return $articles;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}	
	
	public function saveArticle($article) {
		$success = false;
		try {
			$stmt = $this->_conn->prepare ( 'INSERT INTO sha_articles VALUES(:id, :name, :description, :image, :art_usr_id, null)' );
	
			
			$stmt->bindParam ( ':id', $article->getId () );
			$stmt->bindParam ( ':name', $article->getName () );
			$stmt->bindParam ( ':description', $article->getDescription () );
			$stmt->bindParam ( ':image', $article->getImage () );
			$stmt->bindParam ( ':art_usr_id', $article->getUserId ());
			
			$success = $stmt->execute ();
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
		if ($success) {
			$this->insertCategoryRelation($this->_conn->lastInsertId(), $article->getCategories ());
		}
	}
	
	public function modifyArticle($article) {
		$success = false;
		$this->deleteCategoryRelation($article->getId());

		try {
			$stmt = $this->_conn->prepare ( 'REPLACE INTO sha_articles VALUES(:id, :name, :description, :image, :art_usr_id, null)' );
	
				
			$stmt->bindParam ( ':id', $article->getId () );
			$stmt->bindParam ( ':name', $article->getName () );
			$stmt->bindParam ( ':description', $article->getDescription () );
			$stmt->bindParam ( ':image', $article->getImage () );
			$stmt->bindParam ( ':art_usr_id', $article->getUserId ());
				
			$success = $stmt->execute ();
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
		if ($success) {
			$this->insertCategoryRelation($article->getId(), $article->getCategories ());
		}
	}	

	public function deleteArticle($id) {
		$success = false;
		$this->deleteCategoryRelation($id);
		try {
			$stmt = $this->_conn->prepare ( 'DELETE FROM sha_articles WHERE art_id = :id' );
	
	
			$stmt->bindParam ( ':id', $id );
	
			$success = $stmt->execute ();
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}

	}	
	
	private function insertCategoryRelation($art_id, $arr) {
		try {
			foreach ($arr as $cat) {
				if ($cat->getParentId() != null) {
					
					$stmt = $this->_conn->prepare ( 'INSERT INTO sha_art_cat_rel VALUES(:id, :art_id, :cat_id)' );
						
					$id = '';
					$stmt->bindParam ( ':id', $id );
					$stmt->bindParam ( ':art_id', $art_id );
					$stmt->bindParam ( ':cat_id', $cat->getId() );
					
					$stmt->execute ();
				}
			}
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}		
	}

	private function deleteCategoryRelation($art_id) {
		try {
						
			$stmt = $this->_conn->prepare ( 'DELETE FROM sha_art_cat_rel WHERE art_id = :id' );

			$stmt->bindParam ( ':id', $art_id );
				
			$stmt->execute ();
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}	
	
	public function findAllArticles() {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_articles' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->execute ();
			
			$articles = array();
			while ( $row = $stmt->fetch () ) {
				$articles [] = $this->createArticleFromDatabaseRow ( $row );
			}
			return $articles;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function deleteAllArticles() {
		try {
			$stmt = $this->_conn->prepare ( 'DELETE FROM sha_articles' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->execute ();
			
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	// ------------------------ Category ---------------------------- //
	public function findCategoryById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_categories WHERE cat_id=:id' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
			
			$stmt->execute ();
			$row = $stmt->fetch ();
			
			if ($row != null) {
				return $this->createCategoryFromDatabaseRow ( $row );
			}
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	
	public function saveCategory($category) {
		try {
			$stmt = $this->_conn->prepare ( 'INSERT INTO sha_categories VALUES(:id, :name, :parentId)' );
			
			$stmt->execute ( array (
					':id' => $category->getId (),
					':name' => $category->getName (),
					':parentId' => $category->getParentId () 
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findAllCategories() {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_categories' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->execute ();
			
			while ( $row = $stmt->fetch () ) {
				$categories [] = $this->createCategoryFromDatabaseRow ( $row );
			}
			return $categories;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	public function findParentCategories() {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_categories WHERE cat_parent_id IS NULL' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->execute ();
				
			while ( $row = $stmt->fetch () ) {
				$categories [] = $this->createCategoryFromDatabaseRow ( $row );
			}
			return $categories;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	
	public function findSubCategories($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_categories WHERE cat_parent_id=:id' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
	
			$stmt->execute ();
			while ( $row = $stmt->fetch () ) {
				$categories [] = $this->createCategoryFromDatabaseRow ( $row );
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
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
			
			$stmt->execute ();
			$row = $stmt->fetch ();
			
			if ($row != null) {
				return $this->createLocationFromDatabaseRow ( $row );
			}
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function saveLocation($location) {
		try {
			$stmt = $this->_conn->prepare ( 'INSERT INTO sha_locations VALUES(:id, :postcode)' );
			
			$stmt->execute ( array (
					':id' => $location->getId (),
					':postcode' => $location->getPostcode () 
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findAllLocations() {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_locations' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->execute ();
			
			while ( $row = $stmt->fetch () ) {
				$locations [] = $this->createLocationFromDatabaseRow ( $row );
			}
			return $locations;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	// ------------------------ User ---------------------------- //
	
	/**
	 * Returns the user data as a User object.
	 * 
	 * @param string $username
	 * @return \Application\Models\Db\User|NULL
	 */
	public function findUserByUsername($username) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_user WHERE usr_username=:username' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':username', $username );
	
			$stmt->execute ();
			$row = $stmt->fetch ();
	
			if ($row != null) {
				return $this->createUserFromDatabaseRow($row);
			}
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findUserById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_user WHERE usr_id=:id' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
	
			$stmt->execute ();
			$row = $stmt->fetch ();
	
			if ($row != null) {
				return $this->createUserFromDatabaseRow($row);
			}
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}	
	
	
	public function findUserBySession($id)
	{
		try {
			$stmt = $this->_conn->prepare ( 'SELECT u.* FROM sha_session AS s JOIN sha_user AS u ON (session_id=:id AND u.usr_id == s.session_usr_id) ORDER BY create_time DESC LIMIT 1' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
	
			$stmt->execute ();
			$row = $stmt->fetch ();
	
			if ($row != null) {
				return $this->createUserFromDatabaseRow($row);
			}
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	/**
	 * Saves or updates a user based on the given User object.
	 * 
	 * @param User $user
	 */
	public function saveUser(User $user)
	{
		try {
			$stmt = $this->_conn->prepare ( 'INSERT INTO sha_user (usr_username, usr_password, usr_email) VALUES (:username, :password, :email)' );
		
			$stmt->execute ( array (
					':username' => $user->getUsername(),
					':password' => $user->getPassword(),
					':email' => $user->getEmail(),
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	/**
	 * Creates a User object from a database row.
	 * 
	 * @param \stdClass $row
	 * @return User
	 */
	private function createUserFromDatabaseRow($row) {
		$user = User::create()->setId($row->usr_id)->setUsername($row->usr_username)->setPassword($row->usr_password)->setEmail($row->usr_email);
		return $user;
	}
	
	public function findSessionById($id)
	{
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_session WHERE session_id=:id' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
		
			$stmt->execute ();
			$row = $stmt->fetch ();
		
			if ($row != null) {
				return $this->createSessionFromDatabaseRow ( $row );
			}
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}

	/**
	 * Saves or updates a session based on the given Session object.
	 *
	 * @param Session $session
	 */
	public function saveSession(Session $session)
	{
		try {
			$stmt = $this->_conn->prepare ( 'REPLACE INTO sha_session (session_id, session_usr_id, session_state, session_update_time) VALUES (:session, :user, :state, :updateTime)' );
	
			$stmt->execute ( array (
					':session' => $session->getId(),
					':user' => $session->getUserId(),
					':state' => $session->getState(),
					':updateTime' => 'CURRENT_TIMESTAMP'
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	/**
	 * Creates a Session object from a database row.
	 *
	 * @param \stdClass $row
	 * @return Session
	 */
	private function createSessionFromDatabaseRow($row) {
		$session = Session::create()
			->setId($row->session_id)
			->setCreateTime($row->session_create_time)
			->setUpdateTime($row->session_update_time)
			->setState($row->session_state)
			->setUserId($row->session_usr_id);
		return $session;
	}
}
?>