<?php
namespace Application\Controller;

use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Shareshop\Application;
use Application\Plugin\Auth;
use Shareshop\Authorization;
use Application\Models\Db\User;
use Application\Models\Db\Location;

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
		
		$viewData = array('error' => '', 'success' => '', 'form' => array('username' => '', 'email' => '', 'adresse' => '','adresse_lat' => '', 'adresse_lng' => '' ));
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
				if (preg_match('/[^@]{1,63}@[^\.]{1,63}\.[a-zA-Z0-9]{2,63}/', $postData['email'])) {
					if ($this->checkAddress($postData['adresse'])) {
						if ($postData['password'] == $postData['passwordRepeat']) {
							$salt = $this->generateString(10);
							$password = $this->createHash($postData['password']);//, $salt);
							User::create()->setUsername($postData['username'])->setPassword($password)->setEmail($postData['email'])->setSalt($salt)->setState(0)->save();
							$address = split(',',$postData['adresse']);
							$street = trim($address[0]);
							$plz = split(' ', trim($address[1]))[0];
							$town = split(' ', trim($address[1]))[1];
							Location::create()->setStreet($street)->setPostcode($plz)->setTown($town)->setMapLat($postData['adresse_lat'])->setMapLng($postData['adresse_lng'])->save();
							$viewData['success'] = 'Erfolgreich registriert!';
						} else {
							$viewData['error'] = 'Passwörter stimmen nicht überein.';
						}
					} else {
						$viewData['error'] = 'Bitte Adresse im folgenden Format eingeben: Strasse, PLZ Ort.';
					}
					
				} else {
					$viewData['error'] = 'Ungültige E-Mail-Adresse.';
				}
				
			}
		}
		
		$this->view->register('auth/subnavigation', array(), 'subnavigation');
		$this->view->register('auth/register', $viewData);
		$this->view->render();
	}
	
	private function checkAddress($adresse) {
		$arrAddress = split(',',$adresse);
		if (count($arrAddress) != 2 || $arrAddress[0] == null || $arrAddress[1]==null) return false;
		$plz = split(' ', trim($arrAddress[1]))[0];
		if (!is_numeric($plz)) return false;
		return true;
	}
	 
	protected function sendMail()
	{
		
	}
}