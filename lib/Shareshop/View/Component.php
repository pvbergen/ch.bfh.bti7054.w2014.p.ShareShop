<?php
namespace Shareshop\View;

class Component {
	
	protected $_path;
	
	protected $_template;
	
	protected $_data;
	
	public function __construct($path, $template, $data)
	{
		$this->_path = $path;
		$this->_template = $template . '.phtml';
		$this->_data = $data;
	}
	
	public function render()
	{
		$data = $this->_data;
		ob_start();
		include_once $this->_path . '/views/' . $this->_template;
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}