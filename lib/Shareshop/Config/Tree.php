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
	
	public function __get($args)
	{
		if (!isset($this->_data[$args])) {
			return new Tree(array());
		}
		return $this->_data[$args];
	}
		
}