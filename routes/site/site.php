<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\Product;
use \Ecommerce\Model\User;

$app->get('/', function() {
    $products = Product::listAll();
	$page = new Page();
	$page->setTpl("index", ["products"=>Product::checklist($products)]);
});

$app->get('/login', function(){
	$page = new Page();
	$page->setTpl('login', [
		"error"=> User::getMsgError(User::SESSION_ERROR),
		"errorRegister"=> User::getMsgError(User::SESSION_REGISTER_ERROR),
		"registerValues"=> isset($_SESSION['registerValues']) ? $_SESSION['registerValues'] : ["name"=> '', "email"=> '', "phone"=> '', "deslogin"=> '']
	]);
});

$app->post('/login', function(){
	try
	{
		User::loginToEmail($_POST['login'], $_POST['password']);
	}
	catch(\Exception $e)
	{
		User::setMsgError($e->getMessage(), User::SESSION_ERROR);
	}
	header("location: /cart");
	exit;
});

$app->get('/logout', function(){
	User::logout();
	header("Location: /login");
	exit;
});

?>