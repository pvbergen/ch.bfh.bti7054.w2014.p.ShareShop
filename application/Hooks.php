<?php
namespace Application;

use Shareshop\View;
use Shareshop\Request;

class Hooks {
	
	/**
	 * 
	 * 
	 * @param Request $request
	 * @param View $view
	 */
	public function preDispatch(Request $request, View $view)
	{
		//Implement functionality like authentication
	} 
	
	public function postDispatch(Request $request, View $view)
	{
		
	}
}