<?php
include_once 'includeMe.php'; 
use Shareshop\Application;
use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Location;
use Application\Models\Db\Category;
use Application\Models\Db\SearchParameter;
use Application\Models\Db\ArticleDAO;

$article = Article::create()->setDescription("Test")->setName("Staubsauger")->setUserId('1')->setCategoryId('1');
$articleDAO = new ArticleDAO();
$articleDAO->save($article);
$article->setName('Staubsauger II');
$articleDAO->save($article);

$articleDAO->findById(4);

?>