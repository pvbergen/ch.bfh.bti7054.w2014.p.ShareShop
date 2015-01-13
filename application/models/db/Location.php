<?php
namespace Application\Models\Db;
/**
 * ****************************************************************************
 * Location class - represents a place where articles or users can be located.
 * ****************************************************************************
 */
class Location {
	private $id;
	private $street;
	private $postcode;
	private $town;
	private $mapLat;
	private $mapLng;
	
	private function __construct() {
	}
	
	public static function create() {
		return new self();
	}
	
	public function save() {
		$id = DBAccess::getInstance()->saveLocation($this);
		$this->setId($id);
		return $this;
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
	
	public static function findByPostCode($postcode) {
		return DBAccess::getInstance()->findLocationByPostCode($postcode);
	}
	 
	public static function findNearBy($lng, $lat) {
		return DBAccess::getInstance()->findLocationNearBy($lng, $lat);
	}
	
	public static function findAll() {
		return DBAccess::getInstance()->findAllLocations();
	}
	

	
	// ------------------------ GETTER ---------------------------- //
	
	public function getId () {
		return $this->id;
	}
	
	public function getStreet () {
		return $this->street;
	}
	
	public function getPostcode () {
		return $this->postcode;
	}
	
	public function getTown () {
		return $this->town;
	}
	
	public function getMapLat () {
		return $this->mapLat;
	}
	
	public function getMapLng () {
		return $this->mapLng;
	}
	
	// ------------------------ SETTER ---------------------------- //
	
	public function setId ($intId) {
		$this->id=$intId;
		return $this;
	}
	
	public function setStreet ($strStreet) {
		$this->street=$strStreet;
		return $this;
	}
	
	public function setPostcode ($strPostcode) {
		$this->postcode=$strPostcode;
		return $this;
	}
	
	public function setTown ($strTown) {
		$this->town=$strTown;
		return $this;
	}
	
	public function setMapLat($strLat) {
		$this->mapLat=$strLat;
		return $this;
	}
	
	public function setMapLng ($strLng) {
		$this->mapLng=$strLng;
		return $this;
	}
}
