<?php

use \Ecommerce\Page;
use \Ecommerce\Model\Category;

$app->get('/categories/:idcategory/:npage', function($idcategory, $npage){
	$category = new Category();
	$category->get((int)$idcategory);
	$pagination = $category->getProductsPage($npage);
	$pages = [];
	for($i = 1; $i <= $pagination['pages']; $i++)
	{
		array_push($pages, [
			"link"=> '/categories/'.$category->getidcategory().'/'.$i,
			"page"=> $i
		]);
	}

	$page = new Page();
	$page->setTpl("category", array(
		"category"=>$category->getValues(),
		"products"=>$pagination['data'],
		"pages"=>$pages
	));
});

?>