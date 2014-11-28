<?php

namespace Application\Models\Db;

use Application\Models\Db\DBAccess;
use ReflectionClass;

/**
 * ****************************************************************************
 *  DBObject
 * ****************************************************************************
 */
class DBObject {
	protected $_dao;
    
    protected function __construct($dao) {
		$this->_dao = $dao;
	}
	
	protected function save(){
	    $this->_dao->save($this);
	}
	
	protected function delete() {
	    $this->_dao->deleteById($this->getId());
	}
}