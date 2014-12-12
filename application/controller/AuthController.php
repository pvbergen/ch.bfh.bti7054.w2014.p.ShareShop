<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Shareshop\Application;
use Application\Plugin\Auth;
use Shareshop\Authorization;
use Application\Models\Db\User;

class AuthController extends \Shareshop\Controller {
	use Authorization;
	public function indexAction()
	{
		$this->view->register('auth/subnavigation', array(), 'subnavigation');
		$this->view->register('auth/login', array('error' => $this->request->getError()));
		$this->view->render();
	}
	
	public function loginAction()
	{
		$this->request->setController('Article');
		$this->request->setAction('upload');
		Application::getInstance()->forward();
	}
	
	public function logoutAction()
	{
		Auth::getSession()->setState(0)->save();
		setcookie('shareshop', '', '0');
		$this->view->register('auth/subnavigation', array(), 'subnavigation');
		$this->view->register('auth/logout', array('error' => $this->request->getError()));
		$this->view->render();
	}
	
	public function registerAction()
	{
		$session = Auth::getSession();
		if ($session != null && $session->getState() == 1) {
			die('already logged in');
		}
		
		$viewData = array('error' => '', 'success' => '', 'form' => array('username' => '', 'email' => '', 'adresse' => ''));
		$postData = $this->request->getPost();
		if (isset($postData['submitRegister'])) {
			if (
					empty($postData['username']) ||
					empty($postData['password']) ||
					empty($postData['passwordRepeat']) ||
					empty($postData['email']) ||
					empty($postData['adresse'])
			) {
				$viewData['error'] = 'Bitte alle Felder ausfüllen.';
			} else {
				if ($postData['password'] == $postData['passwordRepeat']) {
					$password = $this->createHash($postData['password']);
					User::create()->setUsername($postData['username'])->setPassword($password)->setEmail($postData['email'])->save();
					$viewData['success'] = 'Erfolgreich registriert!';
 				} else {
					$viewData['error'] = 'Passwörter stimmen nicht überein.';
				}
			}
		}
		
		$this->view->register('auth/subnavigation', array(), 'subnavigation');
		$this->view->register('auth/register', $viewData);
		$this->view->render();
	}
}