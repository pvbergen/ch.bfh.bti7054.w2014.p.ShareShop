<?php
namespace Application\Models\Db;

class Exchange {
	
	protected $_exchangeId = -1;
	protected $_requestingUser = -1;
	protected $_answeringUser = -1;
	protected $_requestingRating = null;
	protected $_answeringRating = null;
	protected $_state = 0;
	
	protected $_steps = array();
	
	private function __construct() {
		
	}
	
	/**
	 * Create an empty Article object.
	 */
	public static function create() {
		return new self();
	}
	
	public function save() {
		if ($this->_exchangeId == -1) {
			DBAccess::getInstance()->saveExchange($this);
		} else {
			DBAccess::getInstance()->modifiyExchange($this);
		}
	}
	
	public function modify() {
		$this->save();
	}
	
	public function delete() {
		DBAccess::getInstance()->deleteExchange($this);
	}
	
	public function setId($id)
	{
		$this->_exchangeId = $id;
		return $this;
	}
	
	public function setRequestingUser($id)
	{
		$this->_requestingUser = id;
		return $this;
	}
	
	public function setAnsweringUser($id)
	{
		$this->_answeringUser = $id;
		return $this;
	}
	
	public function setRequestingRating($rating)
	{
		$this->_requestingRating = $rating;
		return $this;
	}
	
	public function setAnsweringRating($rating)
	{
		$this->_answeringRating = $rating;
		return $this;
	}
	
	public function setState($bitmask)
	{
		$this->_state = $bitmask;
		return $this;
	}
	
	public function addStep(RequestStep $step) {
		$this->_steps[] = $step;
		return $this;
	}
	
	public function getId()
	{
		return $this->_exchangeId;
	}
	
	public function getRequestingUser()
	{
		return User::findById($this->_requestingUser);
	}
	
	public function getAnsweringUser()
	{
		return User::findById($this->_answeringUser);
	}
	
	public function getRequestingRating()
	{
		return $this->_requestingRating;
	}
	
	public function getAnsweringRating()
	{
		return $this->_answeringRating;
	}
	
	public function getState()
	{
		return $this->_state;
	}
	
	public function getSteps() {
		return $this->_steps;
	}
}