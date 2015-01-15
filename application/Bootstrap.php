<?php
namespace Application;

use Application\Plugin\Auth;
use Application\Plugin\I18N;

class Bootstrap {
	
	public function initPlugin()
	{
		\Shareshop\Application::getPluginManager()->register(new Auth(), array(\Shareshop\Application::ROUTE_PREDISPATCH));
		\Shareshop\Application::getPluginManager()->register(new I18N(), array(\Shareshop\View::VIEW_PRERENDER));
	}
	
}