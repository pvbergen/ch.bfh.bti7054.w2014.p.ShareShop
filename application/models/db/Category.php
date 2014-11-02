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
	 * Do not use. Categories must exist in database.
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
	
	public function setId ($intId) {
		$this->id=$intId;
		return $this;
	}
	
	public function setName ($strName) {
		$this->name=$strName;
		return $this;
	}
	
	public function setParentId ($intParentId) {
		$this->parentId=$intParentId;
		return $this;
	}
}
