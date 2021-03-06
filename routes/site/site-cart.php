<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\Product;
use \Ecommerce\Model\Cart;
use \Ecommerce\Model\User;

$app->get('/cart', function(){
	try
	{
		$cart = Cart::getFromSession();
		$page = new Page();	
		$page->setTpl('cart', array(
			"cart"=> $cart->getValues(),
			"products"=> $cart->getProducts(),
			"error"=> Cart::getMsgError()
		));
	}
	catch(\Exception $e)
	{
		Cart::setMsgError("Código ".$e->getCode().": ".$e->getMessage());
		header("Location: /error");
		exit;
	}
	
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
	try
	{
		$cart = Cart::getFromSession();
		$cart->setFreight($_POST['zipcode'], $_POST["typeFreight"]);
		header("Location: /cart");
		exit;
	}
	catch(\Exception $e)
	{
		Cart::setMsgError("Código ".$e->getCode().": ".$e->getMessage());
		header("Location: /error");
		exit;
	}
});

?>