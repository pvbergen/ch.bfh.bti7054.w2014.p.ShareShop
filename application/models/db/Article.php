<?php
namespace Application\Models\Db;
use Application\Models\Db\DBAccess;

/**
 * ****************************************************************************
 * Article class - represents an object in the shop that wants to be shared.
 * ****************************************************************************
 */
class Article {
	private $id;
	private $name;
	private $description;
	private $image;
	private $userId;
	private $categories = array();
	private $creationTimestamp;
	
	private function __construct() {
		$this->creationTimestamp = time();
	}
	
	/**
	 * Create an empty Article object.
	 */
	public static function create() {
		return new self();
	}
	
	public function save() {
		DBAccess::getInstance()->saveArticle($this);
	}
	
	/**
	 * Find an article by its unique database id.
	*
	*  @param integer $intId the id of the article to find.
	*  
	*  @return an Article object or null if not found.
	*
	* */
	public static function findById($intId) {
		return DBAccess::getInstance()->findArticleById($intId);
	}
	
	public static function findAll() {
		return DBAccess::getInstance()->findAllArticles();
	}
	
	/**
	 * Find articles by a list of search parameters.
	 *
	 *  @param array $arrSearchParams array of SearchParameter
	 *
	 *  @return array of database ids for Article objects.
	 */
	public static function searchForArticles($arrSearchParams) {
		return DBAccess::getInstance()->searchForArticles($arrSearchParams);
	}
	
	public static function findArticlesByCategoryId($id) {
		return DBAccess::getInstance()->findArticlesByCategoryId($id);
	}
	
	/**
	 * Load articles by article id.
	 *
	 *  @param array $arrArticleIds array of database ids
	 *
	 *  @return array of Article objects.
	 */
	public static function loadArticles($arrArticleIds) {
		return DBAccess::getInstance()->loadArticles($arrArticleIds);
	}
	
	public static function deleteAll() {
		DBAccess::getInstance()->deleteAllArticles();
	}
	
	// ------------------------ GETTER ---------------------------- //
	
	public function getId () {
		return $this->id;
	}
	
	public function getName () {
		return $this->name;
	}
	
	public function getDescription () {
		return $this->description;
	}
	
	public function getImage () {
		return $this->image;
	}
	
	public function getUserId () {
		return $this->userId;
	}
	
	public function getCategories () {
		return $this->categories;
	}
	
	public function getCreationTimestamp () {
		return $this->creationTimestamp;
	}
	
	// ------------------------ SETTER ---------------------------- //
	
	public function setId ($intId) {
		$this->id=$intId;
		return $this;
	}
	
	public function setName ($strName) {
		$this->name=$strName;
		return $this;
	}
	
	public function setDescription ($strDescription) {
		$this->description=$strDescription;
		return $this;
	}
	
	public function setImage ($image) {
		$this->image=$image;
		return $this;
	}
	
	public function setUserId ($UserId) {
		$this->userId = $UserId;
		return $this;
	}
	
	/**
	 * Set all categories for this article.
	 * 
	 *  @param array(Category) $arrCategory array of Category objects (id must exist in database).
	 * 
	 * */
	public function setCategories ($arrCategory) {
		$this->categories=$arrCategory;
		return $this;
	}
}