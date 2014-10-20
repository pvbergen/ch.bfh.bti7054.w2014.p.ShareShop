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
	
	// ------------------------ GETTER ---------------------------- //
	
	public function getId () {
		return $this->id;
	}
	
	public function getPostcode () {
		return $this->postcode;
	}
	
	// ------------------------ SETTER ---------------------------- //
	
	public function setPostcode ($postcode) {
		$this->postcode=$postcode;
	}
}
?>