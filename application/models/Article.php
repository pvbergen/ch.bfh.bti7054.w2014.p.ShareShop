<?php
namespace shareshop\models;
/**
 * ****************************************************************************
 * Article class - represents an object in the shop that wants to be shared
 * ****************************************************************************
 */
class Article {
	protected $id;
	protected $name;
	protected $description;
	protected $image;
	protected $location;
	protected $categories = new array();
	protected $creationTimestamp;
	
	public function __construct($id, $name, $description, $image, $location, $categories) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->image = $image;
		$this->location = $location;
		$this->categories = $categories;
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
	
	public function setName ($name) {
		$this->name=$name;
	}
	
	public function setDescription ($description) {
		$this->description=$description;
	}
	
	public function setImage ($image) {
		$this->image=$image;
	}
	
	public function setLocation ($location) {
		$this->location=$location;
	}
	
	public function setCategories ($categories) {
		$this->categories=$categories;
	}
}
?>