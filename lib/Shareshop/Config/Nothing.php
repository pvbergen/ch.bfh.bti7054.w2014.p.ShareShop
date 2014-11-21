<?php
namespace Shareshop\Config;

class Nothing {
	
	public function __get($args)
	{
		return $this;
	}	
}