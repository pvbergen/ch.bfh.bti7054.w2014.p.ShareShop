<?php
namespace Application\Models\Db;
use Application\Models\Db\Article;
use Application\Models\Db\Category;
use Application\Models\Db\Location;
use Shareshop\Application;

/**
 * ****************************************************************************
 * Database access class - performs all database actions.
 * ****************************************************************************
 */
class DBAccess
{

    protected $_conn;

    protected static $_instance = null;

    const TBL_PREFIX = 'sha_';

    public static function getInstance ()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct ()
    {
        $config = Application::getConfig();
        try {
            $this->_conn = new \PDO(
                    'mysql:host=' . $config->db->host . ';dbname=' .
                             $config->db->database, $config->db->user, 
                            $config->db->password);
            $this->_conn->setAttribute(\PDO::ATTR_ERRMODE, 
                    \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Insert a row into the table.
     *
     * @param array $row
     *            the row to save
     *            
     */
    public function insert ($tblName, $data)
    {
        $paramBindings = array();
        $namedParams = '';
        foreach ($data as $key => $item) {
            $namedParams .= ':' . $key . ',';
            $paramBindings[':' . $key] = $item;
        }
        $namedParams = substr($namedParams, 0, - 1);
        
        try {
            $query = 'INSERT INTO ' . self::TBL_PREFIX . $tblName . ' VALUES(' .
                     $namedParams . ')';
            $stmt = $this->_conn->prepare($query);
            $stmt->execute($paramBindings);
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Update a row on the table.
     *
     * @param Object $obj
     *            the object to save
     *            
     */
    public function update ($tblName, $colPrefix, $data)
    {
        foreach ($data as $key => $item) {
            if ($item == 'id') {
                $idValue = $colPrefix . $key . '=' . $item;
            } else {
                $colValuePairs .= $colPrefix . $key . '=' . $item;
            }
            $paramBindings[':' . $key] = $item;
            next($data);
        }
        
        try {
            $stmt = $this->_conn->prepare(
                    'UPDATE ' . self::TBL_PREFIX . $tblName . ' SET ' .
                             $colValuePairs . 'WHERE ' . $idValue);
            $stmt->execute($paramBindings);
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Find a row by its unique database id.
     *
     * @param number $intId
     *            the id of the row to find.
     *            
     * @return row values.
     *        
     *        
     */
    public function findById ($tblName, $colPrefix, $intId)
    {
        try {
            $stmt = $this->_conn->prepare(
                    'SELECT * FROM ' . self::TBL_PREFIX . $tblName . ' WHERE ' . $colPrefix . 'id=:id');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->bindParam(':id', $intId);
            $stmt->execute();
            
            if ($row = $stmt->fetch()) {
                return $row;
            }
            
            return null;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Find rows by their unique database id.
     *
     * @param number $intId
     *            the id of the row to find.
     *            
     * @return row values.
     *        
     *        
     */
    public static function findByIds ($intId)
    {
        return DBAccess::getInstance()->findById($intId);
    }

    /**
     * Find all rows in this table.
     *
     *
     * @return an Article object or null if not found.
     *        
     *        
     */
    public static function findAll ()
    {}

    /**
     * Delete a row by its unique database id.
     *
     * @param number $intId
     *            the id of the row to delete.
     *            
     *            
     */
    public static function deleteById ($id)
    {
        return DBAccess::getInstance()->deleteById($this);
    }

    /**
     * Delete all rows on a table.
     */
    public static function deleteAll ()
    {
        DBAccess::getInstance()->deleteAll($this->$_tblName);
    }
    
    // ------------------------ Helpers ---------------------------- //
    private function createArticleFromDatabaseRow ($row)
    {
        $article = Article::create()->setId($row->art_id)
            ->setName($row->art_name)
            ->setDescription($row->art_description)
            ->setImage($row->art_image)
            ->setLocation($this->findLocationById($row->art_loc_id));
        
        $catId = $row->art_cat_id;
        while ($catId != null) {
            $category = $this->findCategoryById($catId);
            $categories[] = $category;
            $catId = $category->getParentId();
        }
        $article->setCategories($categories);
        
        return $article;
    }

    private function createCategoryFromDatabaseRow ($row)
    {
        $category = Category::create()->setId($row->cat_id)
            ->setName($row->cat_name)
            ->setParentId($row->cat_parent_id);
        return $category;
    }

    private function createLocationFromDatabaseRow ($row)
    {
        $location = Location::create()->setId($row->loc_id)->setPostcode(
                $row->loc_postcode);
        return $location;
    }
    
    // ------------------------ Article ---------------------------- //
    public function loadArticles ($arrArticleIds)
    {
        try {
            $stmt = $this->_conn->prepare(
                    'SELECT * FROM sha_articles WHERE art_id IN (:ids)');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->bindParam(':ids', implode(',', $arrArticleIds));
            $stmt->execute();
            
            $articles = array();
            while ($row = $stmt->fetch()) {
                $articles[] = $this->createArticleFromDatabaseRow($row);
            }
            return $articles;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function searchForArticles ($arrSearchParams)
    {
        try {
            $paramBindings = array();
            $queryString = '';
            foreach ($arrSearchParams as $param) {
                $queryString .= 'art_' . $param->getField() . ' LIKE :' .
                         $param->getField();
                $queryString .= ' OR ';
                $paramBindings[':' . $param->getField()] = '%' .
                         $param->getSearchString() . '%';
            }
            $queryString = substr($queryString, 0, - 3);
            
            $stmt = $this->_conn->prepare(
                    'SELECT * FROM sha_articles WHERE ' . $queryString);
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->execute($paramBindings);
            
            $articleIds = array();
            while ($row = $stmt->fetch()) {
                $articleIds[] = $row->art_id;
            }
            
            return $articleIds;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function findArticleById ($id)
    {
        try {
            $stmt = $this->_conn->prepare(
                    'SELECT * FROM sha_articles WHERE art_id=:id');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();
            $row = $stmt->fetch();
            
            if ($row != null) {
                return $this->createArticleFromDatabaseRow($row);
            }
            
            return null;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function saveArticle ($article)
    {
        try {
            $stmt = $this->_conn->prepare(
                    'INSERT INTO sha_articles VALUES(:id, :name, :description, :image, :locationId, :categoryId, null)');
            
            $mostSpecificCategoryId = null;
            foreach ($article->getCategories() as $cat) {
                if ($cat->getParentId() == $mostSpecificCategoryId ||
                         $mostSpecificCategoryId == null) {
                    $mostSpecificCategoryId = $cat->getId();
                }
            }
            
            $stmt->bindParam(':id', $article->getId());
            $stmt->bindParam(':name', $article->getName());
            $stmt->bindParam(':description', $article->getDescription());
            $stmt->bindParam(':image', $article->getImage());
            $stmt->bindParam(':locationId', 
                    $article->getLocation()
                        ->getId());
            $stmt->bindParam(':categoryId', $mostSpecificCategoryId);
            
            $stmt->execute();
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function findAllArticles ()
    {
        try {
            $stmt = $this->_conn->prepare('SELECT * FROM sha_articles');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->execute();
            
            $articles = array();
            while ($row = $stmt->fetch()) {
                $articles[] = $this->createArticleFromDatabaseRow($row);
            }
            return $articles;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function deleteAllArticles ()
    {
        try {
            $stmt = $this->_conn->prepare('DELETE FROM sha_articles');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->execute();
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    
    // ------------------------ Category ---------------------------- //
    public function findCategoryById ($id)
    {
        try {
            $stmt = $this->_conn->prepare(
                    'SELECT * FROM sha_categories WHERE cat_id=:id');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();
            $row = $stmt->fetch();
            
            if ($row != null) {
                return $this->createCategoryFromDatabaseRow($row);
            }
            return null;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function saveCategory ($category)
    {
        try {
            $stmt = $this->_conn->prepare(
                    'INSERT INTO sha_categories VALUES(:id, :name, :parentId)');
            
            $stmt->execute(
                    array(
                            ':id' => $category->getId(),
                            ':name' => $category->getName(),
                            ':parentId' => $category->getParentId()
                    ));
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function findAllCategories ()
    {
        try {
            $stmt = $this->_conn->prepare('SELECT * FROM sha_categories');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->execute();
            
            while ($row = $stmt->fetch()) {
                $categories[] = $this->createCategoryFromDatabaseRow($row);
            }
            return $categories;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    
    // ------------------------ Location ---------------------------- //
    public function findLocationById ($id)
    {
        try {
            $stmt = $this->_conn->prepare(
                    'SELECT * FROM sha_locations WHERE loc_id=:id');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();
            $row = $stmt->fetch();
            
            if ($row != null) {
                return $this->createLocationFromDatabaseRow($row);
            }
            return null;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function saveLocation ($location)
    {
        try {
            $stmt = $this->_conn->prepare(
                    'INSERT INTO sha_locations VALUES(:id, :postcode)');
            
            $stmt->execute(
                    array(
                            ':id' => $location->getId(),
                            ':postcode' => $location->getPostcode()
                    ));
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function findAllLocations ()
    {
        try {
            $stmt = $this->_conn->prepare('SELECT * FROM sha_locations');
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->execute();
            
            while ($row = $stmt->fetch()) {
                $locations[] = $this->createLocationFromDatabaseRow($row);
            }
            return $locations;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
?>