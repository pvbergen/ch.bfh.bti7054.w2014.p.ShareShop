<?php
namespace Application\Models\Db;
use Application\Models\Db\DBAccess;

/**
 * ****************************************************************************
 * Article class - represents an object in the shop that wants to be shared.
 * ****************************************************************************
 */
class ArticleTable extends DBObject {
	private $id;
	private $name;
	private $description;
	private $image;
	private $location;
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
	*  @param number $intId the id of the article to find.
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
	 *  @param SearchParameter[] $arrSearchParams array of SearchParameter
	 *
	 *  @return array of database ids for Article objects.
	 */
	public static function searchForArticles($arrSearchParams) {
		return DBAccess::getInstance()->searchForArticles($arrSearchParams);
	}
	
	/**
	 * Load articles by article id.
	 *
	 *  @param number[] $arrArticleIds array of database ids
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
	
	public function getName () {
		return $this->name;
	}
	
	public function getDescription () {
		return $this->description;
	}
	
	public function getImage () {
		return $this->image;
	}
	
	public function getLocation () {
		return $this->location;
	}
	
	public function getCategories () {
		return $this->categories;
	}
	
	public function getCreationTimestamp () {
		return $this->creationTimestamp;
	}
	
	// ------------------------ SETTER ---------------------------- //
	
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
	
	/**
	 * Set the location for this article.
	*
	*  @param Location $objLocation location object (locations must exist in database).
	*
	* */
	public function setLocation ($objLocation) {
		$this->location=$objLocation;
		return $this;
	}
	
	/**
	 * Set all categories for this article.
	 * 
	 *  @param Category[] $arrCategory array of Category objects (categories must exist in database).
	 * 
	 * */
	public function setCategories ($arrCategory) {
		$this->categories=$arrCategory;
		return $this;
	}
}