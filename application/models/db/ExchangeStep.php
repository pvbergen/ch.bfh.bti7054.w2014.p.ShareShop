<?php
namespace Application\Models\Db;

class ExchangeStep {
	
	const TYPE_REQUEST = 1;
	const TYPE_PICK = 2;
	const TYPE_REOFFER = 4;
	const TYPE_EXCHANGE = 8;
	
	protected $_stepId = -1;
	protected $_exchangeId = -1;
	protected $_created = 0;
	protected $_remark = "";
	protected $_type = self::TYPE_REQUEST;
	
	/**
	 * 
	 * @var int[]
	 */
	protected $_articles;
	
	private function __construct() {
		
	}
	
	/**
	 * Create an empty ExchangeStep object.
	 */
	public static function create() {
		return new self();
	}
	
	public static function findByExchangeId($id)
	{
		return DBAccess::getInstance()->findExchangeStepsByExchange($id);
	}
	
	public function save()
	{
		$this->_stepId = DBAccess::getInstance()->saveExchangeStep($this);
		
		if (count($this->_articles) > 0) {
			foreach ($this->_articles as $article) {
				DBAccess::getInstance()->saveExchangeStepArticles($this, $article);
			}
		}
	}
	
	public function setId($id)
	{
		$this->_stepId = $id;
		return $this;
	}
	
	public function setExchangeId($id)
	{
		$this->_exchangeId = $id;
		return $this;
	}
	
	public function setCreated($timestamp)
	{
		$this->_created = $timestamp;
		return $this;
	}
	
	public function setRemark($remark)
	{
		$this->_remark = $remark;
		return $this;
	}
	
	public function setType($type = self::TYPE_REQUEST)
	{
		$this->_type = $type;
		return $this;
	}
	
	public function addArticle($id)
	{
		$this->_articles[] = $id;
		return $this;
	}
	
	public function getId()
	{
		return $this->_stepId;
	}

	public function getExchangeId()
	{
		return $this->_exchangeId;
	}
	
	public function getCreated()
	{
		return $this->_created;
	}
	
	public function getRemark()
	{
		return $this->_remark;
	}
	
	public function getType()
	{
		return $this->_type;
	}
	
	/**
	 * Returns the articles to this step.
	 * 
	 * @return multitype:Article
	 */
	public function getArticles()
	{
		if (count($this->_articles) == 0) {
			$this->_articles = DBAccess::getInstance()->findArticlesIdByExchangeStep($this->getId());
		}
		return $this->_articles;
	}
}