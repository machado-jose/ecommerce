<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\Product;
use \Ecommerce\Model\Category;

$app->get('/', function() {
    $products = Product::listAll();
	$page = new Page();
	$page->setTpl("index", ["products"=>Product::checklist($products)]);
});

$app->get('/categories/:idcategory', function($idcategory){
	$category = new Category();
	$category->get($idcategory);
	$page = new Page();
	$page->setTpl("category", array(
		"category"=>$category->getValues(),
		"products"=>Product::checkList($category->getProducts())
	));
});

?>