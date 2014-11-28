<?php
namespace Application\Models\Db;
use Application\Models\Db\DBAccess;
use ReflectionClass;
use ReflectionMethod;

/**
 * ****************************************************************************
 * Article class - represents an object in the shop that wants to be shared.
 * ****************************************************************************
 */
class GenericDAO {
	protected $_tableName;	
	protected $_colPrefix;
	protected $_dbObject;
	
	protected function __construct($tableName, $colPrefix, $dbObject) {
		$this->_tableName=$tableName;
		$this->_colPrefix=$colPrefix;
		$this->_dbObject=$dbObject;
	} 
	
	public function save($object) {
		if ($object->getId() != null) {
			DBAccess::getInstance()->update($this->_tableName,$this->dbObjectToRow($object));
		} else {
			DBAccess::getInstance()->insert($this->_tableName,$this->dbObjectToRow($object));
		}
	}
	
	public function deleteById($id) {
		DB::deleteById($this->_tableName,$this->_colPrefix, $id);
	}
	
	public function findById($id) {
	    $row = DBAccess::getInstance()->findById($this->_tableName,$this->_colPrefix,$id);
	    return $this->rowToDBObject($row);
	}
	
	public function findByIds($ids) {
		return DB::find($this->_tableName,$this->_colPrefix,$ids);
	}
	
	public function findAll() {
		return DB::findAll($this->_tableName,$this->_colPrefix);
	}
	
	public function deleteAll() {
		DB::deleteAll($this->_tableName,$this->_colPrefix);
	}
	
	
	private function dbObjectToRow($object) {
	        $reflect = new ReflectionClass($object);
	        $properties = $reflect->getProperties();
	        
	        $rowData = array();
	        foreach ($properties as $prop) {
	            $reflectionMethod = new ReflectionMethod($object, 'get' . ucfirst($prop->getName()));
	            $rowData[$prop->getName()] = $reflectionMethod->invoke($object);
	        }
	        return $rowData;
	}
	
	private function rowToDBObject($row) {
	    $reflect = new ReflectionClass($this->_dbObject);
	    $properties = $reflect->getProperties();
	    $object = $reflect->newInstance();
	    
	    print_r($row); 
	   
	    $rowData = array();
	    foreach ($properties as $prop) {
	        $reflectionMethod = new ReflectionMethod($object, 'set' . ucfirst($prop->getName()));
	        $rowData[$prop->getName()] = $reflectionMethod->invoke($object);
	    }
	    return $object;
	}
}