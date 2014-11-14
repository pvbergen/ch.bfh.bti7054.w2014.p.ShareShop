<?php
include_once 'includeMe.php';
use Shareshop\Application;
use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Location;
use Application\Models\Db\Category;
use Application\Models\Db\SearchParameter;

$location = Location::findById ( 1 );
$category = Category::findById ( 1 );

$article = Article::create ()->setName ( 'Zahnbürste' )->setDescription ( '999 Borsten' )->setLocation ( $location )->setCategories ( array (
		$category 
) );
$article2 = Article::create ()->setName ( 'Ecoblade 2039' )->setDescription ( 'Elektrorasenmäher' )->setLocation ( $location )->setCategories ( array (
		$category 
) );

$article->save ();
$article2->save ();

$searchParms [] = new SearchParameter ( 'name', 'Zahn' );
$searchParms [] = new SearchParameter ( 'name', 'Ecoblade' );
$articleIds = Article::searchForArticles ( $searchParms );
$articles = Article::loadArticles ($articleIds);
printObjectArray ( 'Found Articles:', $articles);

Article::deleteAll();

function printObjectArray($functionName, $objectArr) {
	echo '<h1>' . $functionName . '</h1>';
	foreach ($objectArr as $obj ) {
		print_r ( $obj );
	}
}
?>