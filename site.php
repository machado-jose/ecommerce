<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\Product;

$app->get('/', function() {
    $products = Product::listAll();
	$page = new Page();
	$page->setTpl("index", ["products"=>Product::checklist($products)]);
});

?>