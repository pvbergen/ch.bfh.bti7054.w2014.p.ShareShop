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
	
	public static function create() {
		return new self();
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
