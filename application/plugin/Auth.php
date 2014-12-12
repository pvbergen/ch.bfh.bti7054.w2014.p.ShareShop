<?php
namespace Application\Plugin;

use \Shareshop\Application;
use Shareshop\Request;
use \Application\Models\Db\DBAccess;
use Shareshop\Auth\Exception;
use Application\Models\Db\Session;
use Application\Models\Db\User;
use Shareshop\Config\Nothing;
use Shareshop\Config;

class Auth extends \Shareshop\Plugin\AbstractPlugin {
	use \Shareshop\Auth;
	
	protected $_db = null;
	protected $_config = null;
	protected $_state = null;
	
	public function __construct($algorithm = 'md5', $iterations = 1)
	{
		$this->_alg = $algorithm;
		$this->_iter = $iterations;
	}
	
	public function update($event)
	{
		$this->_db = DBAccess::getInstance();
		$this->_config = new Config(APPLICATION_PATH . '/configs/auth.ini');
		$this->_state = Application::getPluginManager()->getState();
				
		session_name('shareshop');
		session_start();
		$sessionId = session_id();
		if ($this->isAuthPath()) {
			$session = Session::create()->findById($sessionId);
			if ($session == null) {
				if (!headers_sent()) {
					$user = $this->authorize($this->_state['request']);
					if ($user != null) {
						Session::create()->setUserId($user->getId())->setState(1)->setId($sessionId)->save();
					}
				}
			} else {
				if($session->getState() != 1) {
						
				}
				$session->save();
			}
		} 	
	}
	
	protected function authorize(Request $request)
	{
		$error = false;
		$postData = $request->getPost();
		if (!empty($postData['submitLogin'])) {
			if (!empty($postData['username'])) {
				if (!empty($postData['password'])) {
					$user = $this->_db->findUserByUsername($postData['username']);
					if ($user !== null) {
						if ($user->getPassword() === $this->createHash($postData['password'])) {
						} else {
							$request->setError("Password incorrect");
						}		
					} else {
						$error = true;
						$request->setError("Username unknown");
					}			
				} else {
					$error = true;
					$request->setError("No password provided");
				}		
			} else {
				$error = true;
				$request->setError("No username provided");
			}
		} else {
			$error = true;
		}
		if ($error) {
			$request->setController('Auth');
			$request->setAction('index');
			$user = null;
		}
		return $user;
		
	}
	
	protected function isAuthPath()
	{
		/* @var $request \Shareshop\Request */
		$request = $this->_state['request'];
		$controller =
		$paths = $this->_config->noauth->paths;
		if ($paths instanceof Nothing) {
			return true;
		}
		$paths = $paths->getArray();
		if (!in_array($request->getController(), array_keys($paths))) {
			return true;
		}
		$actions = $paths[$request->getController()]->getArray();
		if (!in_array($request->getAction(), $actions)) {
			return true;
		}
		return false;
	}
}