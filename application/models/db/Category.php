<?php
namespace Application\Models\Db;

/**
 * ****************************************************************************
 * Category class - represents a article category. Can have a parent category.
 * ****************************************************************************
 */
class Category {
	private $id;
	private $name;
	private $parentId;
	
	private function __construct() {
	}
	
	/**
	 * Static constructor / factory
	 */
	public static function create() {
		return new self();
	}
	
	public function save() {
		DBAccess::getInstance()->saveCategory($this);
	}
	
	public static function findById($id) {
		return DBAccess::getInstance()->findCategoryById($id);
	}
	
	public static function findAll() {
		return DBAccess::getInstance()->findAllCategories();
	}
	
	// ------------------------ GETTER ---------------------------- //
	
	public function getId () {
		return $this->id;
	}
	
	public function getName () {
		return $this->name;
	}
	
	public function getParentId () {
		return $this->parentId;
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
	
	public function setParentId ($parentId) {
		$this->parentId=$parentId;
		return $this;
	}
}
