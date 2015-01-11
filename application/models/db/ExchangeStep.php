<?php
namespace Application\Models\Db;

class ExchangeStep {
	
	const REQUEST = 1;
	const PICK = 2;
	const REOFFER = 4;
	const EXCHANGE = 8;
	
	protected $_stepId = -1;
	protected $_exchangeId = -1;
	protected $_created = 0;
	protected $_remark = "";
	protected $_type = self::REQUEST;
	
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
		
	}
	
	public function setId($id)
	{
		this->_stepId = $id;
		return $this;
	}
	
	public function setExchangeId($id)
	{
		this->_exchangeId = $id;
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
	
	public function setType($type = self::REQUEST)
	{
		$this->_type = $type;
		return $this;
	}
	
	public function addArticle($id)
	{
		$this->_articles[] = $id;
	}
	
	public function getId()
	{
		return $this->_stepId;
	}

	public function getExchangeId()
	{
		return this->_exchangeId;
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
	
	public function getArticles()
	{
		return $this->_articles;
	}
}