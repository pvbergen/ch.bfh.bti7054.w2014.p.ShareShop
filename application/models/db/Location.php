<?php
namespace Application\Models\Db;
/**
 * ****************************************************************************
 * Location class - represents a place where articles can be located.
 * ****************************************************************************
 */
class Location {
	private $id;
	private $name;
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
	
	/**
	 * Find a location by its unique database id.
	*
	*  @param integer $intId the id of the location to find.
	*
	*  @return a Location object or null if not found.
	*
	* */
	public static function findById($intId) {
		return DBAccess::getInstance()->findLocationById($intId);
	}
	
	public static function findAll() {
		return DBAccess::getInstance()->findAllLocations();
	}
	
	// ------------------------ GETTER ---------------------------- //
	
	public function getId () {
		return $this->id;
	}
	
	public function getName () {
		return $this->name;
	}
	
	public function getPostcode () {
		return $this->postcode;
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
	
	public function setPostcode ($strPostcode) {
		$this->postcode=$strPostcode;
		return $this;
	}
}
