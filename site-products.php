<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\Product;

$app->get('/products/:desurl', function($desurl){

	$product = new Product();
	$product->getFromUrl($desurl);
	$page = new Page();
	$page->setTpl("product-detail", array(
		"product"=>$product->getValues(),
		"categories"=>$product->getCategories()
	));
});

?>