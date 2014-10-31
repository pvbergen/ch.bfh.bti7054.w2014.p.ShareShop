<?php
include_once 'includeMe.php'; 
use Shareshop\Application;
use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Location;
use Application\Models\Db\Category;

$location = Location::findById( 1 );
printObjectArray ( 'findLocationById', array ($location));

$category = Category::findById(1);
printObjectArray ( 'findCategoryById', array ($category));

$article = Article::create()->setName('Zahnbürste')
							->setDescription('999 Borsten')
							->setLocation($location)
							->setCategories(array($category));
$article->save();

$article = Article::findById( 4 );
printObjectArray ('findArticleById', array ($article));

$articles = Article::findAll();
printObjectArray ( 'findAllArticles', $articles );

function printObjectArray($functionName, $objectArr) {
	echo '<h1>' . $functionName . '</h1>';
	foreach ( $objectArr as $obj ) {
		print_r ( $obj );
	}
}
?>