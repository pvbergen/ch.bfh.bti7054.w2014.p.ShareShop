<?php
namespace Application\Models\Db;
use Application\Models\Db\DBAccess;

/**
 * ****************************************************************************
 * Article class - represents an object in the shop that wants to be shared.
 * ****************************************************************************
 */
class Article
{

    private $id;

    private $name;

    private $description;

    private $image;

    private $locationId;

    private $categoryId;

    private $creationTimestamp;

    private function __construct (){}

    public static function create ()
    {
        return new self();
    }
    
    // ------------------------ GETTER ---------------------------- //
    public function getId ()
    {
        return $this->id;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function getDescription ()
    {
        return $this->description;
    }

    public function getImage ()
    {
        return $this->image;
    }

    public function getLocationId ()
    {
        return $this->locationId;
    }

    public function getCategoryId ()
    {
        return $this->categoryId;
    }

    public function getCreationTimestamp ()
    {
        return $this->creationTimestamp;
    }
    
    // ------------------------ SETTER ---------------------------- //
    public function setId ($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setName ($strName)
    {
        $this->name = $strName;
        return $this;
    }

    public function setDescription ($strDescription)
    {
        $this->description = $strDescription;
        return $this;
    }

    public function setImage ($image)
    {
        $this->image = $image;
        return $this;
    }

    public function setLocationId ($id)
    {
        $this->locationId = $id;
        return $this;
    }

    public function setCategoryId ($id)
    {
        $this->categoryId = $id;
        return $this;
    }
}