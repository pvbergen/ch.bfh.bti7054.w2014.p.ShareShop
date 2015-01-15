<?php
namespace Application\Plugin;


use Shareshop\Application;
use Shareshop\Language;
use Application\Models\Db\User;
class I18N extends \Shareshop\Plugin\AbstractPlugin {
		
	public function __construct()
	{
	}
	
	public function update($event)
	{
		$language = Application::getLanguage();
		if (Auth::getSession() != null) {
			$user = User::findBySessionId(Auth::getSession()->getId());
			$language = $user->getLanguage();
		}
		$state = Application::getPluginManager()->getState();
		$state['view']->setLanguage(new Language($language));
	}
}