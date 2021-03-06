<?php
namespace Application\Plugin;

use Shareshop\Application;
use Shareshop\Request;
use Shareshop\Authorization;
use Shareshop\Authorization\Exception;
use Shareshop\Config\Nothing;
use Shareshop\Config;
use Application\Models\Db\Session;
use Application\Models\Db\User;

class Auth extends \Shareshop\Plugin\AbstractPlugin {
	use Authorization;
	
	protected $_config = null;
	
	/**
	 * 
	 * @var Session
	 */
	protected static $_session = null;
	
	public function __construct($algorithm = 'md5', $iterations = 1)
	{	
		$this->_alg = $algorithm;
		$this->_iter = $iterations;
	}
	
	public function update($event)
	{
		$this->_config = new Config(APPLICATION_PATH . '/configs/auth.ini', APPLICATION_ENV);
		if ($this->_config->auth->disabled == 1) {
			return;
		}
		session_name('shareshop');
		session_set_cookie_params("86400");
		session_start();
		$sessionId = session_id();
		$request = Application::getPluginManager()->getState()['request'];
		Auth::$_session = Session::create()->findById($sessionId);
		if(Auth::$_session != null && Auth::$_session->getState() == 1) {
			Auth::$_session->save();
		} else {
			Auth::$_session = null;
		}
		if ($this->isAuthPath($request)) {
			$postData = $request->getPost();
			if (isset($postData['submitLogin'])) {
				$user = $this->authorize($request);
				if ($user != null) {
					Auth::$_session = Session::create()->setUserId($user->getId())->setState(1)->setId($sessionId);
					Auth::$_session->save();
				}
			} elseif (Auth::$_session == null) {
				$request->setController('Auth');
				$request->setAction('index');
			}
		} 	
	}
	
	/**
	 * 
	 * @return \Application\Models\Db\Session
	 */
	public static function getSession()
	{
		return Auth::$_session;
	}
	
	protected function authorize(Request $request)
	{
		$user = null;
		$postData = $request->getPost(); 
		if (!empty($postData['username'])) {
			if (!empty($postData['password'])) {
				$user = User::create()->findByUsername($postData['username']);
				if ($user !== null) {
					if ($user->getPassword() !== $this->createHash($postData['password'], $user->getSalt())) {
						$user = null;
						$request->setError("Password incorrect");
					}		
				} else {
					$request->setError("Username unknown");
				}			
			} else {
				$request->setError("No password provided");
			}		
		} else {
			$request->setError("No username provided");
		}
	
		if ($user == null) {
			$request->setController('Auth');
			$request->setAction('index');
		}
		return $user;
		
	}
	
	protected function isAuthPath(Request $request)
	{
		$paths = $this->_config->noauth->paths;
		if ($paths instanceof Nothing) {
			return true;
		}	
		$paths = $paths->getArray();
		if (!in_array($request->getController(), array_keys($paths))) {
			return true;
		}
		if (empty($paths[$request->getController()]))  {
			return false;	
		}
		$actions = $paths[$request->getController()]->getArray();
		if (!in_array($request->getAction(), $actions)) {
			return true;
		}
		return false;
	}
}