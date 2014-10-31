<?php
namespace Application\Models\Db;
/**
 * ****************************************************************************
 * Location class - represents a place where articles can be located
 * ****************************************************************************
 */
class Location {
	private $id;
	private $postcode;
	
	private function __construct() {
	}
	
	/**
	 * Static constructor / factory
	 */
	public static function create() {
		return new self();
	}
	
	public function save() {
		DBAccess::getInstance()->saveLocation($this);
	}
	
	public static function findById($id) {
		return DBAccess::getInstance()->findLocationById($id);
	}
	
	public static function findAll() {
		return DBAccess::getInstance()->findAllLocations();
	}
	
	// ------------------------ GETTER ---------------------------- //
	
	public function getId () {
		return $this->id;
	}
	
	public function getPostcode () {
		return $this->postcode;
	}
	
	// ------------------------ SETTER ---------------------------- //
	
	public function setId ($id) {
		$this->id=$id;
		return $this;
	}
	
	public function setPostcode ($postcode) {
		$this->postcode=$postcode;
		return $this;
	}
}
