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
		$this->view->register('auth/subnavigation', array(), 'subnavigation');
		$this->view->register('auth/logout', array('error' => $this->request->getError()));
		$this->view->render();
	}
	
	public function profileAction()
	{
		$user = User::findBySessionId(Auth::getSession()->getId());
		$location = Location::findById($user->getLocId());
		$viewData = array('error' => '', 'success' => '', 'form' => array('email' => '', 'adresse' => '','adresse_lat' => '', 'adresse_lng' => '', 'language' => 'de_de' ));
		$postData = $this->request->getPost();
		if (isset($postData['submitRegister'])) {
			if (
					empty($postData['email']) ||
					empty($postData['adresse']) || 
					empty($postData['language'])
			) {
				$viewData['error'] = 'Bitte alle Felder ausfüllen.';
			} else {
				if (preg_match('/[^@]{1,63}@[^\.]{1,63}\.[a-zA-Z0-9]{2,63}/', $postData['email'])) {
					if ($this->checkAddress($postData['adresse'])) {
						$address = split(',',$postData['adresse']);
						$street = trim($address[0]);
						$plz = split(' ', trim($address[1]))[0];
						$town = split(' ', trim($address[1]))[1];
						$mapLat = $postData['adresse_lat'] != null ? $postData['adresse_lat'] : '0.0' ;
						$mapLng = $postData['adresse_lng'] != null ? $postData['adresse_lat'] : '0.0' ;
						$location->setStreet($street)->setPostcode($plz)->setTown($town)->setMapLat($mapLat)->setMapLng($mapLng)->save();
						$user->setEmail($postData['email'])->setLanguage($postData['language'])->save();
						$viewData['success'] = 'Erfolgreich geändert!';
					} else {
						$viewData['error'] = 'Bitte Adresse im folgenden Format eingeben: Strasse, PLZ Ort.';
					}
				} else {
					$viewData['error'] = 'Ungültige E-Mail-Adresse.';
				}
			}
		}
		
		$address = $location->getStreet() . ', ' . $location->getPostcode() . ' ' . $location->getTown();
		$viewData['form'] = array('language' => $user->getLanguage(), 'email' => $user->getEmail(), 'adresse' => $address, 'adresse_lat' => $mapLat, 'adresse_lng' => $mapLng);
		$this->view->register('navigation/staticSubnavigation', array('profile' => true), 'subnavigation');
		$this->view->register('auth/profile', $viewData);
		$this->view->render();
		
	}
	
	public function passwordAction()
	{
		
		$user = User::findBySessionId(Auth::getSession()->getId());
		$location = Location::findById($user->getLocId());
		$viewData = array('error' => '', 'success' => '', 'form' => array('email' => '', 'adresse' => '','adresse_lat' => '', 'adresse_lng' => '', 'language' => 'de_de' ));
		$postData = $this->request->getPost();
		if (isset($postData['submitRegister'])) {
			if (
					empty($postData['oldPassword']) ||
					empty($postData['password']) ||
					empty($postData['passwordRepeat'])
			) {
				$viewData['error'] = 'Bitte alle Felder ausfüllen.';
			} else {
				$auth = new Auth();
				if ($auth->createHash($postData['oldPassword'], $user->getSalt()) == $user->getPassword()) {
					if ($postData['password'] == $postData['passwordRepeat']) {
						$salt = $this->generateString(10);
						$password = $this->createHash($postData['password'], $salt);
						$user->setPassword($password)->setSalt($salt)->save();
						$viewData['success'] = 'Erfolgreich geändert!';
					} else {
						$viewData['error'] = 'Passwörter stimmen nicht überein.';
					}
				} else {
					$viewData['error'] = 'Das alte Passwort ist nicht korrekt.';
				}
			}
		}		
		$this->view->register('navigation/staticSubnavigation',  array('profile' => true), 'subnavigation');
		$this->view->register('auth/password', $viewData);
		$this->view->render();
	}
	
	public function registerAction()
	{
		$session = Auth::getSession();
		if ($session != null && $session->getState() == 1) {
			die('already logged in');
		}
		$viewData = array('error' => '', 'success' => '', 'form' => array('username' => '', 'email' => '', 'adresse' => '','adresse_lat' => '', 'adresse_lng' => '', 'language' => Application::getLanguage() ));
		$postData = $this->request->getPost();
		if (isset($postData['submitRegister'])) {
			if (
					empty($postData['username']) ||
					empty($postData['password']) ||
					empty($postData['passwordRepeat']) ||
					empty($postData['email']) ||
					empty($postData['adresse'])
			) {
				$viewData['error'] = 'Bitte alle Felder ausfüllen';
			} else {
				if (preg_match('/[^@]{1,63}@[^\.]{1,63}\.[a-zA-Z0-9]{2,63}/', $postData['email'])) {
					if ($this->checkAddress($postData['adresse'])) {
						if ($postData['password'] == $postData['passwordRepeat']) {
							$userDB = User::findByUsername($postData['username']);
							if ($userDB == null || $userDB->getUsername() != strtolower($postData['username'])) {
								$address = split(',',$postData['adresse']);
								$street = trim($address[0]);
								$plz = split(' ', trim($address[1]))[0];
								$town = split(' ', trim($address[1]))[1];
								$mapLat = $postData['adresse_lat'] != null ? $postData['adresse_lat'] : '0.0' ;
								$mapLng = $postData['adresse_lng'] != null ? $postData['adresse_lat'] : '0.0' ;
								$location = Location::create()->setStreet($street)->setPostcode($plz)->setTown($town)->setMapLat($mapLat)->setMapLng($mapLng)->save();
								$salt = $this->generateString(10);
								$password = $this->createHash($postData['password'], $salt);
								User::create()->setUsername($postData['username'])->setPassword($password)->setEmail($postData['email'])->setSalt($salt)->setLanguage($postData['language'])->setState(0)->setLocationId($location->getId())->save();
		
								$viewData['success'] = 'Erfolgreich registriert';
							} else {
								$viewData['error'] = 'Dieser Username existiert bereits';
							}
						} else {
							$viewData['error'] = 'Passwörter stimmen nicht überein';
						}
					} else {
						$viewData['error'] = 'Bitte Adresse im folgenden Format eingeben: Strasse, PLZ Ort';
					}
						
				} else {
					$viewData['error'] = 'Ungültige E-Mail-Adresse';
				}
			}
		}
		if (!empty($viewData['error'])) {
			$viewData['form']['username'] = $postData['username'];
			$viewData['form']['email'] = $postData['email'];
			$viewData['form']['adresse'] = $postData['adresse'];
			$viewData['form']['language'] = $postData['language'];
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
}