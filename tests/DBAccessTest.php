<?php
use Shareshop\Application;
use Application\Models\Db\DBAccess;
use Application\Models\Db\Article;
use Application\Models\Db\Location;
use Application\Models\Db\Category;

define("BASE_PATH", dirname(dirname(__FILE__)));
define("APPLICATION_PATH", BASE_PATH . '\application');
define("APPLICATION_ENV", 'development');

require_once BASE_PATH . '/lib/SplClassLoader.php';

$autoloader = new SplClassLoader('Shareshop', BASE_PATH . '/lib');
$autoloader->register();

$autoloader = new SplClassLoader('Application', BASE_PATH);
$autoloader->register();

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