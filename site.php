<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\Product;
use \Ecommerce\Model\Category;
use \Ecommerce\Model\Cart;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Address;

$app->get('/', function() {
    $products = Product::listAll();
	$page = new Page();
	$page->setTpl("index", ["products"=>Product::checklist($products)]);
});

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

$app->get('/products/:desurl', function($desurl){

	$product = new Product();
	$product->getFromUrl($desurl);
	$page = new Page();
	$page->setTpl("product-detail", array(
		"product"=>$product->getValues(),
		"categories"=>$product->getCategories()
	));
});

$app->get('/cart', function(){
	$cart = Cart::getFromSession();
	$page = new Page();	
	$page->setTpl('cart', array(
		"cart"=> $cart->getValues(),
		"products"=> $cart->getProducts(),
		"error"=> Cart::getMsgError()
	));
});

$app->get('/cart/:idproduct/add', function($idproduct){
	$product = new Product();
	$product->get($idproduct);
	$cart = Cart::getFromSession();
	$cart->addProduct($product);
	header("Location: /cart");
	exit;
});

$app->post('/cart/:idproduct/add', function($idproduct){
	$product = new Product();
	$product->get($idproduct);
	$cart = Cart::getFromSession();
	$qtd = (isset($_POST['qtd'])) ? $_POST['qtd'] : 1;
	for($i = 0; $i < $qtd; $i++)
	{
		$cart->addProduct($product);
	}
	header("Location: /cart");
	exit;
});

$app->get('/cart/:idproduct/minus', function($idproduct){
	$product = new Product();
	$product->get($idproduct);
	$cart = Cart::getFromSession();
	$cart->removeProduct($product);
	header("Location: /cart");
	exit;
});

$app->get('/cart/:idproduct/remove', function($idproduct){
	$product = new Product();
	$product->get($idproduct);
	$cart = Cart::getFromSession();
	$cart->removeProduct($product, true);
	header("Location: /cart");
	exit;
});

$app->post('/cart/freight', function(){
	$cart = Cart::getFromSession();
	$cart->setFreight($_POST['zipcode']);
	header("Location: /cart");
	exit;
});

$app->get('/checkout', function(){
	User::verifyLogin(false);
	$address = new Address();
	$cart = Cart::getFromSession();
	$page = new Page();
	$page->setTpl('checkout', array(
		"cart"=> $cart->getValues(),
		"address"=> $address->getValues()
	));
});

$app->get('/login', function(){
	$page = new Page();
	$page->setTpl('login', [
		"error"=> User::getMsgError()
	]);
});

$app->post('/login', function(){
	try
	{
		User::loginToEmail($_POST['login'], $_POST['password']);
	}
	catch(\Exception $e)
	{
		User::setMsgError($e->getMessage());
	}
	header("location: /checkout");
	exit;
});

$app->get('/logout', function(){
	User::logout();
	header("Location: /login");
	exit;
});

?>