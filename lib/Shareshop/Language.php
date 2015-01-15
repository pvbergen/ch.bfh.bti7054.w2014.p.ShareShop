<?php
namespace Shareshop;

use Shareshop\Config\Nothing;
class Language {
	
	protected $_data = null;
	
	public function __construct($lang = "de_de", $path = "") {
		if (empty($path)) {
			$path = APPLICATION_PATH . '/translation/strings.ini';
		}
		$this->_data = new Config($path, $lang);
	}
	
	public function e($key) 
	{
		if ($this->_data->{$key} instanceof Nothing ) {
			echo $key;
		} else {
			echo $this->_data->{$key};
		}
		
	}
	
}