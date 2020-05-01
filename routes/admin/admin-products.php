<?php 

use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Product;

$app->get('/admin/products/:idproduct/delete', function($idproduct){
	User::verifyLogin();
	$product = new Product();
	$product->get((int)$idproduct);
	$product->delete();
	header("Location: /admin/products");
	exit;
});

$app->get('/admin/products/create', function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl('products-create');
});

$app->post('/admin/products/create', function(){

	User::verifyLogin();
	$product = new Product();

	if(!isset($_POST['desproduct']) || $_POST['desproduct'] === '')
	{
		throw new Exception("Precisa adicionar o nome do produto.");
	}

	if(!isset($_POST['vlprice']) || $_POST['vlprice'] === '')
	{
		throw new Exception("Precisa adicionar o valor do produto.");
	}

	if(!isset($_POST['vlwidth']) || $_POST['vlwidth'] === '')
	{
		throw new Exception("Precisa adicionar a largura do produto.");
	}

	if(!isset($_POST['vlheight']) || $_POST['vlheight'] === '')
	{
		throw new Exception("Precisa adicionar a altura do produto.");
	}

	if(!isset($_POST['vllength']) || $_POST['vllength'] === '')
	{
		throw new Exception("Precisa adicionar o comprimento do produto.");
	}

	if(!isset($_POST['vlweight']) || $_POST['vlweight'] === '')
	{
		throw new Exception("Precisa adicionar o peso do produto.");
	}

	if(!isset($_POST['description']) || $_POST['description'] === '')
	{
		throw new Exception("Precisa adicionar a descrição");
	}

	if(!isset($_POST['desurl']) || $_POST['desurl'] === '')
	{
		throw new Exception("Precisa adicionar a URL do produto.");
	}

	$product->setDatas($_POST);
	if(!$product->save()) throw new Exception("Produto já existe no Banco de Dados");
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

	if(!isset($_POST['desproduct']) || $_POST['desproduct'] === '')
	{
		throw new Exception("Precisa adicionar o nome do produto.");
	}

	if(!isset($_POST['vlprice']) || $_POST['vlprice'] === '')
	{
		throw new Exception("Precisa adicionar o valor do produto.");
	}

	if(!isset($_POST['vlwidth']) || $_POST['vlwidth'] === '')
	{
		throw new Exception("Precisa adicionar a largura do produto.");
	}

	if(!isset($_POST['vlheight']) || $_POST['vlheight'] === '')
	{
		throw new Exception("Precisa adicionar a altura do produto.");
	}

	if(!isset($_POST['vllength']) || $_POST['vllength'] === '')
	{
		throw new Exception("Precisa adicionar o comprimento do produto.");
	}

	if(!isset($_POST['vlweight']) || $_POST['vlweight'] === '')
	{
		throw new Exception("Precisa adicionar o peso do produto.");
	}

	if(!isset($_POST['description']) || $_POST['description'] === '')
	{
		throw new Exception("Precisa adicionar a descrição");
	}

	if(!isset($_POST['desurl']) || $_POST['desurl'] === '')
	{
		throw new Exception("Precisa adicionar a URL do produto.");
	}
	
	$product->setDatas($_POST);
	if(!$product->update()) throw new Exception("O nome do produto ou a URL já está sendo usado.");
	;
	
	if($_FILES["file"]["name"] !== '' && $_FILES["file"]["tmp_name"] !== ''){
		$product->setPhoto($_FILES["file"]);
	} 

	header("Location: /admin/products");
	exit;
});

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

?>