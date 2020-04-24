<?php 

use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Product;

$app->get('/admin/products', function(){

	User::verifyLogin();
	
	$search = (isset($_GET['search'])) ? $_GET['search'] : '';
    $nrpage = (isset($_GET['page'])) ? $_GET['page'] : 1;

    $pagination = ($search != '') ? Product::getProductsPageSearch($search, $nrpage) : Product::getProductsPage($nrpage); 

    $pages = [];

    for($x = 1; $x <= $pagination['pages']; $x++)
    {
        array_push($pages, [
            'href'=> '/admin/products?'.http_build_query([
                "page"=>$x,
                "search"=>$search
            ]),
            'text'=> $x
        ]);

    }

	$page = new PageAdmin();
	$page->setTpl('products',[
		"products"=> $pagination['data'],
        "search"=> $search,
        "pages"=> $pages
	]);
});

$app->get('/admin/products/create', function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl('products-create');
});

$app->post('/admin/products/create', function(){

	User::verifyLogin();
	$product = new Product();

	if(!isset($_POST['description']) || $_POST['description'] === '')
	{
		throw new Exception("Precisa adicionar a descrição");
		
	}

	$product->setDatas($_POST);
	$product->save();
	$product->saveDescription($_POST['description']);
	header("Location: /admin/products");
	exit;
});

$app->get('/admin/products/:idproduct/delete', function($idproduct){
	User::verifyLogin();
	$product = new Product();
	$product->get((int)$idproduct);
	$product->deleteDescription();
	$product->delete();
	header("Location: /admin/products");
	exit;
});

$app->get('/admin/products/:idproduct', function($idproduct){
	User::verifyLogin();
	$product = new Product();
	$product->get((int)$idproduct);
	$product->getDescription((int)$idproduct);
	$page = new PageAdmin();
	$page->setTpl('products-update', ["product"=>$product->getValues()]);
});

$app->post('/admin/products/:idproduct', function($idproduct){
	User::verifyLogin();
	$product = new Product();
	$product->get((int)$idproduct);

	if(isset($_POST['description']) && $_POST['description'] !== '')
	{
		$product->saveDescription($_POST['description']);
	}
	
	$product->setDatas($_POST);
	$product->save();
	
	if($_FILES["file"]["name"] !== '' && $_FILES["file"]["tmp_name"] !== ''){
		$product->setPhoto($_FILES["file"]);
	} 

	header("Location: /admin/products");
	exit;
});

?>