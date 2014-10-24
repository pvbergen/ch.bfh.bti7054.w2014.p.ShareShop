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
	private $locationId;
	private $categoryId;
	private $creationTimestamp;
	
	public function __construct($id, $name, $description, $image, $locationId, $categoryId) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->image = $image;
		$this->locationId = $locationId;
		$this->categoryId = $categoryId;
		$this->creationTimestamp = time();
	}
	
	/**
	 * Static constructor / factory
	 */
	public static function create() {
		$instance = new self(null,null,null,null,null,null);
		return $instance;
	}
	
	public static function save() {
		DBAccess::getInstance()->saveArticle($this);
	}
	
	public static function readById($id) {
		return DBAccess::getInstance()->readArticleById($id);
	}
	
	public static function readAll() {
		return DBAccess::getInstance()->readAllArticles();
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
	
	public function getLocationId () {
		return $this->locationId;
	}
	
	public function getCategoryId () {
		return $this->categoryId;
	}
	
	public function getCreationTimestamp () {
		return $this->creationTimestamp;
	}
	
	// ------------------------ SETTER ---------------------------- //
	
	public function setId ($id) {
		$this->id=$id;
		return $this;
	}
	
	public function setName ($name) {
		$this->name=$name;
		return $this;
	}
	
	public function setDescription ($description) {
		$this->description=$description;
		return $this;
	}
	
	public function setImage ($image) {
		$this->image=$image;
		return $this;
	}
	
	public function setLocationId ($locationId) {
		$this->locationId=$locationId;
		return $this;
	}
	
	public function setCategoryId ($categoryId) {
		$this->categoryId=$categoryId;
		return $this;
	}
}
?>