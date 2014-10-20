<?php
use Application\Models\Db\DBAccess;

$dbAccess = new DBAccess();

$article = $dbAccess->getArticleById(4);
printObjectArray('getArticleById', array($article));

$articles = $dbAccess->getAllArticles();
printObjectArray('getAllArticles', $articles);

$location = $dbAccess->getLocationById(1);
printObjectArray('getLocationById', array($location));

$category = $dbAccess->getCategoryById(1);
printObjectArray('getCategoryById', array($category));
	
function printObjectArray($functionName, $objectArr) {
	echo '<h1>' . $functionName . '</h1>';
	foreach($objectArr as $obj){
		print_r($obj);
	}
}
?>