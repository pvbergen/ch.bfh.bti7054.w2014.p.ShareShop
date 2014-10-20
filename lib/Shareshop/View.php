<?php
namespace Shareshop;

use Shareshop\View\Component;
class View {
	/**
	 * The default component area of the layout.
	 * 
	 * @var string
	 */
	const DEFAULT_COMPONENT = 'content';
	
	/**
	 * The path of the views directory.
	 * 
	 * @var string
	 */
	protected $_path = APPLICATION_PATH;
	
	/**
	 * Wether the current call is an ajax call.
	 * 
	 * @var boolean
	 */
	protected $_isAjax = false;
	
	/**
	 * The default layout file.
	 * 
	 * @var string
	 */
	protected $_layout = 'index.phtml';
	
	/**
	 * Array of registered view components
	 * 
	 * @var Component[]
	 */
	protected $_registeredViews = array();

	/**
	 * Create a new View object.
	 * @param string $path 		Optional. The path of the application directory.
	 * @param boolean $isAjax	Optional. True, if call is an ajax call (only DEFAULT_COMPONENT is rendered), 
	 * 							false otherwise (full layout is rendered).
	 */
	public function __construct($path = APPLICATION_PATH, $isAjax = false)
	{
		$this->_path = $path;
		$this->_isAjax = $isAjax;
	}
	
	/**
	 * The layout to render.
	 * 
	 * @param string $layout
	 */
	public function setLayout($layout)
	{
		$this->_layout = $layout . '.phtml';
	}
	
	/**
	 * Register a new component for a location in the layout.
	 * 
	 * @param string $view 		The view to render.
	 * @param unknown $data 	The data provided to the view.
	 * @param unknown $location	Optional. The location to render the view.
	 */
	public function register($view, $data, $location = self::DEFAULT_COMPONENT)
	{
		$singleView = new Component($this->_path, $view, $data);
		$this->_registeredViews[$location] = $singleView;
	}
	
	/**
	 * Render the registered view on their corresponding location.
	 * If rendering is set to AJAX, only the default component is rendered.
	 */
	public function render()
	{
		if ($this->_isAjax) {
			echo $this->_registeredViews[self::DEFAULT_COMPONENT]->render();
			return;
		}
		$components = array();
		foreach($this->_registeredViews as $location => $singleView) {
			$components[$location] = $singleView->render();
		}
		include_once $this->_path . '/layouts/' . $this->_layout;
	}

	/**
	 * Define, whether to render the content as AJAX or not.
	 * 
	 * @param string $isAjax 	True, if rendering is for AJAX, false otherwise.
	 */
	public function renderAsAjax($isAjax = false)
	{
		$this->_isAjax = $isAjax;
	}
	
	/**
	 * Whether the rendering is AJAX or not.
	 * @return boolean
	 */
	public function isAjax()
	{
		return $this->_isAjax;
	}
}