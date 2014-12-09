<?php
namespace Application\Models\Db;

/**
 * ****************************************************************************
 * Search parameter - can be used in search function.
 * ****************************************************************************
 */
class SearchParameter {
	/**
	 * The field to scan.
	 *
	 * @var $field
	 */
	protected $_field;
	/**
	 * The string to search for.
	 *
	 * @var $searchString
	 */
	protected $_searchString;
	
	public function __construct($field, $searchString) {
		$this->_field = $field;
		$this->_searchString = $searchString;
	}
	
	public function getField () {
		return $this->_field;
	}
	
	public function getSearchString() {
		return $this->_searchString;
	}
	
}