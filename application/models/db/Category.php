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
	
	/**
	 * Find a category by its unique database id.
	*
	*  @param integer $intId the id of the category to find.
	*
	*  @return a Category object or null if not found.
	*
	* */
	public static function findById($intId) {
		return DBAccess::getInstance()->findCategoryById($intId);
	}
	
	public static function findAll() {
		return DBAccess::getInstance()->findAllCategories();
	}
	
	public static function findAllParents() {
		return DBAccess::getInstance()->findParentCategories();
	}
	
	public static function findAllSubCategories($id) {
		return DBAccess::getInstance()->findSubCategories($id);
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
