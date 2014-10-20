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
	
	public function setName ($name) {
		$this->name=$name;
	}
	
	public function setParentId ($parentId) {
		$this->parentId=$parentId;
	}
}
?>