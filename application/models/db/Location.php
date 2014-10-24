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
	
	public function __construct($id, $postcode) {
		$this->id = $id;
		$this->postcode = $postcode;
	}
	
	/**
	 * Static constructor / factory
	 */
	public static function create() {
		$instance = new self(null, null);
		return $instance;
	}
	
	public static function save() {
		DBAccess::getInstance()->saveLocation($this);
	}
	
	public static function readById($id) {
		return DBAccess::getInstance()->readLocationById($id);
	}
	
	public static function readAll() {
		return DBAccess::getInstance()->readAllLocations();
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
