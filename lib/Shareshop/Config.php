<?php
namespace Shareshop;

use Shareshop\Config\Tree;
class Config extends Tree {
	
	protected $_data = array();
		
	public function __construct($path, $targetSection = 'production')
	{
		$break = false;		
		$configData = parse_ini_file($path, true);
		
		$tree = array();
		foreach($configData as $section => $data) {
			if ($section == $targetSection) {
				$break = true;
			}
			if ($section != $targetSection && $break) {
				break;
			}
			foreach($data as $key => $value)
			{
				$helper = &$this->_data;
				foreach(explode('.', $key) as $kPart) {
					$helper = &$helper[$kPart];
				}
				$helper = $value;
			}
			unset($helper);
		}
		foreach($this->_data as $key => $values) {
			if (is_array($values)) {
				$this->_data[$key] = new Tree($values);
			} else {
				$this->_data[$key] = $values;
			}
			
		}
	}	
}