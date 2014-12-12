<?php
namespace Application\Models\Db;
/**
 * ****************************************************************************
 * Session class - represents a session of an authorized, registered user
 * ****************************************************************************
 */
class Session {
	private $id;
	private $usr_id;
	private $create_time;
	private $update_time;
	private $state;
	
	private function __construct() {
	}
	
	public static function create() {
		return new self();
	}
	
	public function save() {
		DBAccess::getInstance()->saveSession($this);
	}
	
	/**
	 * Find a location by its unique database id.
	*
	*  @param integer $intId the id of the location to find.
	*
	*  @return Session
	*
	* */
	public static function findById($intId) {
		return DBAccess::getInstance()->findSessionById($intId);
	}
	
	// ------------------------ GETTER ---------------------------- //
	
	public function getId () {
		return $this->id;
	}
	
	public function getUserId () {
		return $this->usr_id;
	}
	
	public function getState () {
		return $this->state;
	}
	
	public function getCreateTime () {
		return $this->create_time;
	}
	
	public function getUpdateTime () {
		return $this->update_time;
	}
	
	// ------------------------ SETTER ---------------------------- //
	
	public function setId ($intId) {
		$this->id=$intId;
		return $this;
	}
	
	public function setUserId ($intUsrId) {
		$this->usr_id=$intUsrId;
		return $this;
	}
	
	public function setState ($intState) {
		$this->state=$intState;
		return $this;
	}
	
	public function setCreateTime ($strCreateTime) {
		$this->create_time=$strCreateTime;
		return $this;
	}
	
	public function setUpdateTime ($strUpdateTime) {
		$this->update_time=$strUpdateTime;
		return $this;
	}
}
