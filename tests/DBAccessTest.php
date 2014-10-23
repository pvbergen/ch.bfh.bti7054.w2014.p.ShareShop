<?php
use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Location;
use Application\Models\Db\Category;

$article = Article::create()->setName('Zahnbürste')
							->setDescription('999 Borsten')
							->setLocationId('1')
							->setCategoryId('1');
$article->save();

$article = Article::readById( 4 );
printObjectArray ('getArticleById', array ($article));

$articles = Article::readAll();
printObjectArray ( 'getAllArticles', $articles );

$location = Location::readById( 1 );
printObjectArray ( 'getLocationById', array ($location));

$category = Category::readById(1);
printObjectArray ( 'getCategoryById', array ($category));

function printObjectArray($functionName, $objectArr) {
	echo '<h1>' . $functionName . '</h1>';
	foreach ( $objectArr as $obj ) {
		print_r ( $obj );
	}
}
?>