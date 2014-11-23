<?php
namespace Application\Models\Db;
use Application\Models\Db\DBAccess;

/**
 * ****************************************************************************
 * Article class - represents an object in the shop that wants to be shared.
 * ****************************************************************************
 */
class ArticleDAO extends GenericDAO
{

    const TBL_NAME = 'article';

    const COL_PREFIX = 'art_';

    public function __construct ()
    {
        parent::__construct(self::TBL_NAME, self::COL_PREFIX);
    }

    /**
     * Find articles by a list of search parameters.
     *
     * @param array $arrSearchParams
     *            array of SearchParameter
     *            
     * @return array of database ids for Article objects.
     */
    public function searchForArticles ($arrSearchParams)
    {
        return DBAccess::getInstance()->searchForArticles($arrSearchParams);
    }
}