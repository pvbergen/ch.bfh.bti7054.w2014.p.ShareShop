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
			$this->_conn->exec("set names utf8");
		} catch ( \PDOException $e ) {
			echo $e->getMessage ();
		}
	}

	// ------------------------ Helpers ---------------------------- //
	private function createArticleFromDatabaseRow($row) {
		$article = Article::create ()->setId ( $row->art_id )->setName ( $row->art_name )->setDescription ( $row->art_description )->setImage ( $row->art_image )->setUserId( $row->art_usr_id );
		//print_r($this->findCategoriesByArticle($article->getId()));
		$article->setCategories($this->findCategoriesByArticle($article->getId()));
		return $article;
	}
	
	private function createCategoryFromDatabaseRow($row) {
		$category = Category::create ()->setId ( $row->cat_id )->setName ( $row->cat_name )->setParentId ( $row->cat_parent_id );
		return $category;
	}
	
	private function createLocationFromDatabaseRow($row) {
		$location = Location::create ()->setId( $row->loc_id )->setStreet($row->loc_street)->setPostcode($row->loc_postcode)->setTown($row->loc_town)->setMapLat($row->loc_mapLat)->setMapLng($row->loc_mapLng);
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
			
			$articles = array();
			$index = 0;
			while ( $row = $stmt->fetch () ) {
				$articles[$index++] = $this->createArticleFromDatabaseRow ( $row );
			}

			return $articles;
			
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
			$stmt = $this->_conn->prepare ( 'UPDATE sha_articles SET art_name = :name, art_description = :description, art_image = :image, art_usr_id = :art_usr_id WHERE art_id = :id' );
	
				
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

	public function deleteArticle($article) {
		$success = false;
		$id = $article->getId();
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
	
	private function findCategoriesByArticle($id) {
		try {

			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_art_cat_rel WHERE art_id=:id' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
		
			$stmt->execute ();
				
			$categories = array();
			$index = 0;
			while ( $row = $stmt->fetch () ) {
				$categories[$index++] = $this->findCategoryById($row->cat_id);
			}
			return $categories;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}		
	}
	// ------------------------ Location ---------------------------- //
	public function findLocationById($id) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_location WHERE loc_id=:id' );
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

	public function findLocationByPostCode($postcode) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_location WHERE loc_postcode=:postcode' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':postcode', $postcode );
		
			$stmt->execute ();
			$locations = array();
			while ( $row = $stmt->fetch () ) {
				$locations [] = $this->createLocationFromDatabaseRow ( $row );
			}
			return $locations;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}		
	}
	
	public function findLocationNearBy($lng, $lat) {
		try {
			$lngFrom = $lng - 0.07;
			$lngTo = $lng + 0.07;
			$latFrom = $lat - 0.07;
			$latTo = $lat + 0.07;			
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_location WHERE loc_mapLng BETWEEN :lngFrom AND :lngTo AND loc_mapLat BETWEEN :latFrom AND :latTo ' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':lngFrom', $lngFrom );
			$stmt->bindParam ( ':lngTo', $lngTo );
			$stmt->bindParam ( ':latFrom', $latFrom );
			$stmt->bindParam ( ':latTo', $latTo );
			$stmt->execute ();
			$locations = array();
			while ( $row = $stmt->fetch () ) {
				$locations [] = $this->createLocationFromDatabaseRow ( $row );
			}
			return $locations;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}		
	}
	
	
	public function saveLocation($location) {
		try {
			$stmt = $this->_conn->prepare ( 'INSERT INTO sha_location VALUES(:id, :street, :postcode, :town, :mapLat, :mapLng)' );
			$stmt->execute ( array (
					':id' => $location->getId (),
					':street' => $location->getStreet(),
					':postcode' => $location->getPostcode(),
					':town' => $location->getTown(),
					':mapLat' => $location->getMapLat(),
					':mapLng' => $location->getMapLng() 
			) );
			return $this->_conn->lastInsertId();
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function modifyLocation($location) {
		try {
			$stmt = $this->_conn->prepare ( 'UPDATE sha_location SET loc_street = :street, loc_postcode = :postcode, loc_town = :town, loc_mapLat = :mapLat, loc_mapLng = :mapLng WHERE loc_id = :id' );
			$stmt->execute ( array (
					':id' => $location->getId (),
					':street' => $location->getStreet(),
					':postcode' => $location->getPostcode(),
					':town' => $location->getTown(),
					':mapLat' => $location->getMapLat(),
					':mapLng' => $location->getMapLng()
			) );
			return $this->_conn->lastInsertId();
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findAllLocations() {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_location' );
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
			$stmt = $this->_conn->prepare ( 'SELECT u.* FROM sha_session AS s JOIN sha_user AS u ON (session_id=:id AND u.usr_id = s.session_usr_id) ORDER BY session_create_time DESC LIMIT 1' );
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
	
	public function findUserByLocId($locid) {
		try {
			$stmt = $this->_conn->prepare ( 'SELECT * FROM sha_user WHERE usr_loc_id=:locid' );
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':locid', $locid );
		
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
	 * Saves a user based on the given User object.
	 * 
	 * @param User $user
	 */
	public function saveUser(User $user)
	{
		try {
			$stmt = $this->_conn->prepare ( 'INSERT INTO sha_user (usr_username, usr_password, usr_email, usr_salt, usr_language, usr_loc_id) VALUES (:username, :password, :email, :salt, :langauge, :usr_loc_id)' );
		
			$stmt->execute ( array (
					':username' => $user->getUsername(),
					':password' => $user->getPassword(),
					':email' => $user->getEmail(),
					':salt' => $user->getSalt(),
					':language' => $user->getLanguage(),
					':usr_loc_id' => $user->getLocId()
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	/**
	 * Updates a user based on the given User object.
	 *
	 * @param User $user
	 */
	public function modifyUser(User $user)
	{
		try {
			$stmt = $this->_conn->prepare ( 'UPDATE sha_user SET usr_password = :password, usr_salt = :salt, usr_email = :email, usr_language = :language, usr_loc_id = :usr_loc_id WHERE usr_id = :id' );
	
			$stmt->execute ( array (
					':id' => $user->getId(),
					':password' => $user->getPassword(),
					':salt' => $user->getSalt(),
					':email' => $user->getEmail(),
					':language' => $user->getLanguage(),
					':usr_loc_id' => $user->getLocId()
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
		$user = User::create()->setId($row->usr_id)->setUsername($row->usr_username)->setPassword($row->usr_password)->setSalt($row->usr_salt)->setEmail($row->usr_email)->setLanguage($row->usr_language)->setLocationId($row->usr_loc_id);
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
	
	// ------------------------ Exchange ---------------------------- //
	
	public function findExchangeById($id)
	{
		try {
			$stmt = $this->_conn->prepare (
					'SELECT * FROM sha_exchange'
					.' WHERE exchange_id = :id'
			);
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $id );
	
			$stmt->execute ();
			$row = $stmt->fetch();
				
			if ($row != null) {
				return $this->createExchangeFromDatabaseRow( $row );
			}
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findExchangeByUser($userId)
	{
		try {
			$stmt = $this->_conn->prepare (
					'SELECT * FROM sha_exchange'
					.' WHERE answering_user = :id OR requesting_user = :id'
			);
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $userId );
	
			$stmt->execute ();
				
			$exchanges = array();
			if ($stmt->rowCount() > 0) {
				while (($row = $stmt->fetch()) != null) {
					$exchanges[] = $this->createExchangeFromDatabaseRow( $row );
				}
			}
			return $exchanges;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findActiveExchangeByArticleAndUser($userId, $articleId)
	{
		try {
			$stmt = $this->_conn->prepare (
					'SELECT e.* FROM sha_exchange AS e JOIN ('
					.' SELECT s.exchange_id FROM sha_exchange_step AS s JOIN sha_exchange_step_item AS i '
					.' ON (i.step_id = s.step_id AND i.art_id = :articleId) ) AS si '
					.' ON (e.exchange_id = si.exchange_id AND e.requesting_user = :userId AND state = 0)'
			);
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':userId', $userId );
			$stmt->bindParam ( ':articleId', $articleId );
	
			$stmt->execute ();
			$row = $stmt->fetch ();
	
			if ($row != null) {
				return $this->createExchangeFromDatabaseRow( $row );
			}
			return null;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	public function findExchangeStepsByExchange($exchangeId)
	{
		try {
			$stmt = $this->_conn->prepare (
					'SELECT * FROM sha_exchange_step'
					.' WHERE exchange_id = :id ORDER BY step_created DESC'
			);
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $exchangeId );
	
			$stmt->execute ();
	
			$steps = array();
			if ($stmt->rowCount() > 0) {
				while (($row = $stmt->fetch()) != null) {
					$steps[] = $this->createExchangeStepFromDatabaseRow( $row );
				}
			}
			return $steps;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	
	public function findArticlesIdByExchangeStep($stepId)
	{
		try {
			$stmt = $this->_conn->prepare (
					'SELECT a.* FROM sha_articles AS a JOIN sha_exchange_step_item AS es'
					.' ON (a.art_id = es.art_id AND es.step_id = :id)'
			);
			$stmt->setFetchMode ( \PDO::FETCH_OBJ );
			$stmt->bindParam ( ':id', $stepId );
	
			$stmt->execute ();
	
			$articles = array();
			if ($stmt->rowCount() > 0) {
				while (($row = $stmt->fetch()) != null) {
					$articles[] = $this->createArticleFromDatabaseRow( $row );
				}
			}
			return $articles;
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	/**
	 * Saves an exchange based on the given Exchange object.
	 *
	 * @param Exchange $exchange
	 * @return int The insert id.
	 */
	public function saveExchange(Exchange $exchange)
	{
		try {
			$stmt = $this->_conn->prepare (
					'INSERT INTO sha_exchange'
					. ' (exchange_id, requesting_user, answering_user, requesting_rating, answering_rating, state)'
					. ' VALUES (:id, :requestingUser, :answeringUser, :requestingRating, :answeringRating, :state)' );
	
			$stmt->execute ( array (
					':id' => $exchange->getId(),
					':requestingUser' => $exchange->getRequestingUser()->getId(),
					':answeringUser' => $exchange->getAnsweringUser()->getId(),
					':requestingRating' => $exchange->getRequestingRating(),
					':answeringRating' => $exchange->getAnsweringRating(),
					':state' => $exchange->getState()
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
		return $this->_conn->lastInsertId();
	}
	
	/**
	 * Updates an exchange based on the given Exchange object.
	 *
	 * @param Exchange $exchange
	 */
	public function modifyExchange(Exchange $exchange)
	{
		try {
			$stmt = $this->_conn->prepare (
					'UPDATE sha_exchange SET'
					.' requesting_rating = :requestingRating,'
					.' answering_rating = :answeringRating, state = :state'
					.' WHERE exchange_id = :id '
			);
	
			$stmt->execute ( array (
					':id' => $exchange->getId(),
					':requestingRating' => $exchange->getRequestingRating(),
					':answeringRating' => $exchange->getAnsweringRating(),
					':state' => $exchange->getState()
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	/**
	 * Saves an exchange step based on the given ExchangeStep object.
	 *
	 * @param ExchangeStep $step
	 * @return int The last insert id.
	 */
	public function saveExchangeStep(ExchangeStep $step)
	{
		try {
			$stmt = $this->_conn->prepare (
					'REPLACE INTO sha_exchange_step'
					. ' (exchange_id, step_created, step_remark, step_type)'
					. ' VALUES (:exchangeId, :created, :remark, :type)' );
	
			$stmt->execute ( array (
					':exchangeId' => $step->getExchangeId(),
					':created' => $step->getCreated(),
					':remark' => $step->getRemark(),
					':type' => $step->getType()
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
		return $this->_conn->lastInsertId();
	}
	
	/**
	 * Saves the items of an exchange step based on the given ExchangeStep object.
	 *
	 * @param ExchangeStep $step
	 * @param int $articleId
	 */
	public function saveExchangeStepArticles(ExchangeStep $step, $articleId)
	{
		try {
			$stmt = $this->_conn->prepare (
					'INSERT INTO sha_exchange_step_item'
					. ' (step_id, art_id)'
					. ' VALUES (:stepId, :artId)' );
	
			$stmt->execute ( array (
					':stepId' => $step->getId(),
					':artId' => $articleId,
			) );
		} catch ( \PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
	
	/**
	 * Creates an Exchange object from a database row.
	 *
	 * @param \stdClass $row
	 * @return Exchange
	 */
	private function createExchangeFromDatabaseRow($row) {
		$exchange = Exchange::create()
		->setId($row->exchange_id)
		->setRequestingUser($row->requesting_user)
		->setAnsweringUser($row->answering_user)
		->setRequestingRating($row->requesting_rating)
		->setAnsweringRating($row->answering_rating)
		->setState($row->state);
		return $exchange;
	}
	
	/**
	 * Creates an ExchangeStep object from a database row.
	 *
	 * @param \stdClass $row
	 * @return ExchangeStep
	 */
	private function createExchangeStepFromDatabaseRow($row) {
		$step = ExchangeStep::create()
		->setId($row->step_id)
		->setExchangeId($row->exchange_id)
		->setCreated($row->step_created)
		->setRemark($row->step_remark)
		->setType($row->step_type);
		return $step;
	}
	
}
?>