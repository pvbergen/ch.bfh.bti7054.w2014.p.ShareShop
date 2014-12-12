<?php
namespace Shareshop;

trait Authorization {
	
	protected $_alg = "md5";
	
	protected $_iter = 1;
	
	public function setAlgorithm($algorithm = 'md5')
	{
		$this->_alg = $algorithm;
	}
	
	public function setIterations($iterations = 1)
	{
		$this->_iter = $iterations;
	}
	
	public function createHash($pass, $salt = '') 
	{
		if (empty($this->_alg)) {
			return $pass;
		}
			
		$hash = $pass;
		for($i=0; $i < $this->_iter; $i++) {
			$hash = hash($this->_alg, $hash.$salt);	
		}		
		return $hash;
	}
	
	public function generateString($length, $characters = 'abcdefghijklmnopqrstuvwxyz1234567890') 
	{
		$s = '';
		$cl = strlen($characters)-1;
    	for ($i = 0; $i < $length; $i++) {
    		$s .= $characters[mt_rand(0, $cl)];
    	}
    	return $s;
	}

}