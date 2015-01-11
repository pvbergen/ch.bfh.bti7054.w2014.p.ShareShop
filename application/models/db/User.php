<?php
namespace Application\Models\Db;
/**
 * ****************************************************************************
 * User class - represents a registered user
 * ****************************************************************************
 */
class User {
	private $id;
	private $username;
	private $password;
	private $salt;
	private $email;
	private $state;
	private $locId;
	
	private function __construct() {
	}
	
	public static function create() {
		return new self();
	}
	
	public function save() {
		DBAccess::getInstance()->saveUser($this);
	}
	
	/**
	 * Find a user by its unique database id.
	*
	*  @param integer $intId the id of the location to find.
	*
	*  @return User A user object or null if not found.
	*
	* */
	public static function findById($intId) {
		return DBAccess::getInstance()->findUserById($intId);
	}
	
	public static function findBySessionId($strId) {
		return DBAccess::getInstance()->findUserBySession($strId);
	}

	public static function findByUsername($username) {
		return DBAccess::getInstance()->findUserByUsername($username);
	}
	
	public static function findByLocId($locid) {
		return DBAccess::getInstance()->findUserByLocId($locid);
	}
	
	// ------------------------ GETTER ---------------------------- //
	
	public function getId () {
		return $this->id;
	}
	
	public function getUsername () {
		return $this->username;
	}
	
	public function getPassword () {
		return $this->password;
	}
	
	public function getSalt () {
		return $this->salt;
	}
	
	public function getEmail () {
		return $this->email;
	}
	
	public function getState () {
		return $this->state;
	}
	
	public function getLocId() {
		return $this->locId;
	}
	// ------------------------ SETTER ---------------------------- //
	
	public function setId ($intId) {
		$this->id=$intId;
		return $this;
	}
	
	public function setUsername ($strUsername) {
		$this->username=$strUsername;
		return $this;
	}
	
	public function setPassword ($strPassword) {
		$this->password=$strPassword;
		return $this;
	}
	
	public function setSalt ($strSalt) {
		$this->salt=$strSalt;
		return $this;
	}
	
	public function setEmail ($strEmail) {
		$this->email=$strEmail;
		return $this;
	}
	
	public function setState ($intState) {
		$this->state=$intState;
		return $this;
	}
	
	public function setLocationId($locId) {
		$this->locId = $locId;
		return $this;
	}
}
