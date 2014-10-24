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
	
	public function __construct($id, $name, $parentId) {
		$this->id = $id;
		$this->name = $name;
		$this->parentId = $parentId;
	}
	
	/**
	 * Static constructor / factory
	 */
	public static function create() {
		$instance = new self(null,null,null);
		return $instance;
	}
	
	public static function save() {
		DBAccess::getInstance()->saveCategory($this);
	}
	
	public static function readById($id) {
		return DBAccess::getInstance()->readCategoryById($id);
	}
	
	public static function readAll() {
		return DBAccess::getInstance()->readAllCategories();
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
?>