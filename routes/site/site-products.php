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

$app->get('/products', function(){

	$nrpage = (isset($_GET['page'])) ? $_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : '';

	$pagination = ($search != '') ? Product::getProductsPageSearch($search, $nrpage) : Product::getProductsPage($nrpage);

	$pages = [];

	for($x = 1; $x <= $pagination['pages']; $x++)
    {
        array_push($pages, [
            'link'=> '/products?'.http_build_query([
                "page"=>$x
            ]),
            'page'=> $x
        ]);

    }

	$page = new Page();
	$page->setTpl("products", array(
		"products"=>$pagination['data'],
		"search"=> $search,
		"pages"=>$pages
	));

});

?>