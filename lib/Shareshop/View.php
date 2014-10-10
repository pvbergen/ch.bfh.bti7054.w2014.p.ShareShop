<?php
namespace Shareshop;

use Shareshop\View\SingleView;
class View {
	
	protected $_path = '';
	
	protected $_layout = 'index.phtml';
	
	protected $_registeredViews = array();
	
	public function __construct($path)
	{
		$this->_path = $path;
	}
	
	public function setLayout($layout)
	{
		$this->_layout = $layout . '.phtml';
	}
	
	public function register($view, $data, $location = 'content')
	{
		$singleView = new SingleView($this->_path, $view, $data);
		$this->_registeredViews[$location] = $singleView;
	}
	
	public function render()
	{
		$components = array();
		foreach($this->_registeredViews as $location => $singleView) {
			$components[$location] = $singleView->render();
		}
		include_once $this->_path . '/layouts/' . $this->_layout;
	}
	
}