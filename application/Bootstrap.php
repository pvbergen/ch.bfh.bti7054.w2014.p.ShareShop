<?php
namespace Application;

use Application\Plugin\Auth;

class Bootstrap {
	
	public function initPlugin()
	{
		\Shareshop\Application::getPluginManager()->register(new Auth(), array(\Shareshop\Application::ROUTE_PREDISPATCH));
	}
	
}