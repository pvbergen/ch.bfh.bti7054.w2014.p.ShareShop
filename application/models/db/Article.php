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
	 * Static constructor / factory
	 */
	public static function create() {
		return new self();
	}
	
	public function save() {
		DBAccess::getInstance()->saveArticle($this);
	}
	
	public static function findById($id) {
		return DBAccess::getInstance()->readArticleById($id);
	}
	
	public static function findAll() {
		return DBAccess::getInstance()->readAllArticles();
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
	
	public function setLocation ($location) {
		$this->location=$location;
		return $this;
	}
	
	public function setCategories ($categories) {
		$this->categories[]=$categories;
		return $this;
	}
}