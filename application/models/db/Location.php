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
	 * Do not use. Locations must exist in database.
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
	
	public function setId ($intId) {
		$this->id=$intId;
		return $this;
	}
	
	public function setPostcode ($strPostcode) {
		$this->postcode=$strPostcode;
		return $this;
	}
}
