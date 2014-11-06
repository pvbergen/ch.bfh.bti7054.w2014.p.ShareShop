<?php
namespace Application\Models\Db;
use Application\Models\Db\DBAccess;

/**
 * ****************************************************************************
 * Article class - represents an object in the shop that wants to be shared
 * ****************************************************************************
 */
class Article {
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
	
	public static function findById($id) {
		return DBAccess::getInstance()->findArticleById($id);
	}
	
	public static function findAll() {
		return DBAccess::getInstance()->findAllArticles();
	}
	
	public static function findByText() {
			
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
	
	/*
	 * Set the location for this article
	*
	*  $objLocation Location object (must exist in database).
	*
	* */
	public function setLocation ($objLocation) {
		$this->location=$objLocation;
		return $this;
	}
	
	/*
	 * Set all categories for this article
	 * 
	 *  $arrCategory array of Category objects (must exist in database).
	 * 
	 * */
	public function setCategories ($arrCategory) {
		$this->categories=$arrCategory;
		return $this;
	}
}