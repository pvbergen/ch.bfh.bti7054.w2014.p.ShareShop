<?php
namespace Application\Plugin;

use \Shareshop\Application;
class Auth extends \Shareshop\Plugin\AbstractPlugin {
	use \Shareshop\Auth;
	
	public function __construct($algorithm = 'md5', $iterations = 1)
	{
		$this->_alg = $algorithm;
		$this->_iter = $iterations;
	}
	
	public function update($event)
	{
		$config = Application::getConfig();
		$state = Application::getPluginManager()->getState();
		/* @var $request \Shareshop\Request */
		$request = $state['request'];
		//print_r($request);
		//echo '<br />' . $request->getController();
		//$controller = $request->getController();
		/*if (in_array($controller, explode(",", $config->auth->paths))) {
			//echo 'auth path';
		}*/
	}
	
	public function authorize()
	{
		
	}
}