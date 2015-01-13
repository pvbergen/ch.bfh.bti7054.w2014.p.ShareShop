<?php
namespace Application\Models\Db;

class Exchange {
	
	const STATE_ACTIVE = 0;
	const STATE_COMPLETED = 1;
	const STATE_CANCELLED = 2;
	
	protected $_exchangeId = -1;
	protected $_requestingUser = -1;
	protected $_answeringUser = -1;
	protected $_requestingRating = null;
	protected $_answeringRating = null;
	protected $_state = self::STATE_ACTIVE;
	
	/**
	 * 
	 * @var ExchangeStep[]
	 */
	protected $_steps = array();
	
	private function __construct() {
		
	}

	/**
	 * Returns the exchange by the given id.
	 *
	 * @param int $id			The exchange id.
	 * @return Exchange			The exchange or null.
	 */
	public static function findById($id)
	{
		return DBAccess::getInstance()->findExchangeById($id);
	}
	
	/**
	 * 
	 * Returns an active Exchange for the article and the user,
	 * where the given article is requested and the given user is the requesting user 
	 * or null.
	 *  
	 * @param Article $article	The article.
	 * @param User $user		The user.
	 * @return Exchange 		The active exchange or null, if no active Exchange can be found.
	 */
	public static function findActiveByArticleAndUser(Article $article, User $user)
	{
		return DBAccess::getInstance()->findActiveExchangeByArticleAndUser($user->getId(), $article->getId());
	}
	
	/**
	 * Returns all exchanges (active, cancelled or completed) the given user is taking part in.
	 *
	 * @param User $user		The user.
	 * @return Exchange[]		All exchanges the user is participating in.
	 */
	public static function findByUser(User $user)
	{
		return DBAccess::getInstance()->findExchangeByUser($user->getId());
	}
	
	/**
	 * Create an empty Article object.
	 */
	public static function create() {
		return new self();
	}

	public function save() {
		if ($this->_exchangeId > 0) {
			DBAccess::getInstance()->modifyExchange($this);
		} else {
			$this->_exchangeId = DBAccess::getInstance()->saveExchange($this);
		}
	
		foreach($this->_steps as $step) {
			$step->setExchangeId($this->_exchangeId);
			$step->save();
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
		$this->_requestingUser = $id;
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
	
	public function setState($bitmask = self::STATE_ACTIVE)
	{
		$this->_state = $bitmask;
		return $this;
	}
	
	public function addStep(ExchangeStep $step) {
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
	
	/**
	 * Returns all completed steps of this exchange.
	 *  
	 * @return multitype:\Application\Models\Db\ExchangeStep
	 */
	public function getSteps() {
		if (count($this->_steps) == 0) {
			$this->_steps = ExchangeStep::findByExchangeId($this->_exchangeId);
		}
		return $this->_steps;
	}
}