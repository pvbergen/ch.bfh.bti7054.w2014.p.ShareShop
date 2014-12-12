<?php
namespace Shareshop\Config;

class Tree {
	
	protected $_data = array();
	
	public function __construct($data)
	{
		foreach($data as $key => $values) {
			if (!is_array($values)) {
				$this->_data[$key] = $values;
			} else {
				$this->_data[$key] = new Tree($values);
			}
			
		}
	}
	
	/**
	 * 
	 * 
	 * @param string $args
	 * @return mixed|\Shareshop\Config\Nothing|multitype:
	 */
	public function __get($args)
	{
		if (!isset($this->_data[$args])) {
			return new Nothing();
		}
		return $this->_data[$args];
	}
	
	public function getArray()
	{
		return $this->_data;
	}
}