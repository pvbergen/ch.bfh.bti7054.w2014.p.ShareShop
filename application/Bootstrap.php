<?php
namespace Application;

use Shareshop\View;
use Shareshop\Request;

class Bootstrap {
	
	/**
	 * 
	 * 
	 * @param Request $request
	 * @param View $view
	 */
	public function preDispatch(Request $request, View $view)
	{
		//Implement functionality like authentication, adding components to the view etc.
	} 
	
	public function postDispatch(Request $request, View $view)
	{
		
	}
}