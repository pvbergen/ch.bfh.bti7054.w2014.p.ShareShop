<?php
namespace Application\Models\Db;

/**
 * ****************************************************************************
 * Article class - represents an object in the shop that wants to be shared
 * ****************************************************************************
 */
class Article2 {
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
	
	public function setName ($name) {
		$this->name=$name;
	}
	
	public function setDescription ($description) {
		$this->description=$description;
	}
	
	public function setImage ($image) {
		$this->image=$image;
	}
	
	public function setLocationId ($locationId) {
		$this->locationId=$locationId;
	}
	
	public function setCategoryId ($categoryId) {
		$this->categoryId=$categoryId;
	}
}