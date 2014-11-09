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
	 * @var $query
	 */
	protected $_query;
	
	public function __construct($field, $query) {
		$this->_field = $field;
		$this->_query = $query;
	}
	
	public function getField () {
		return $this->_field;
	}
	
	public function getQuery () {
		return $this->_query;
	}
	
}