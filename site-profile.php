<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Order;
use \Ecommerce\Model\Cart;

$app->get('/profile', function(){
	User::verifyLogin(false);
	$user = User::getFromSession();
	$page = new Page();
	$page->setTpl("profile", array(
		"user"=> $user->getValues(),
		"profileMsg"=> User::getMsgSuccess(),
		"profileError"=> User::getMsgError(User::SESSION_REGISTER_ERROR)
	));
});

$app->post('/profile', function(){

	User::verifyLogin(false);

	if(!isset($_POST['desperson']) || $_POST['desperson'] === '')
	{
		User::setMsgError("O Nome Completo é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /profile");
		exit;
	}
	if(!isset($_POST['desemail']) || $_POST['desemail'] === '')
	{
		User::setMsgError("O Email é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /profile");
		exit;
	}
	if(!isset($_POST['deslogin']) || $_POST['deslogin'] === '')
	{
		User::setMsgError("O Login é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /profile");
		exit;
	}

	$user = User::getFromSession();

	if($_POST['deslogin'] !== $user->getdeslogin())
	{
		if(User::checkLoginExists($_POST['deslogin']))
		{
			User::setMsgError("O Login informado já existe.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile");
			exit;
		}
	}

	if($_POST['desemail'] !== $user->getdesemail())
	{
		if(User::checkEmailExists($_POST['desemail']))
		{
			User::setMsgError("O Email informado já existe.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile");
			exit;
		}
	}
	
	$_POST['inadmin'] = $user->getinadmin();
	$_POST['despassword'] = $user->getdespassword();

	$user->setDatas($_POST);
	$user->update();
	$user->updateSession();

	User::setMsgSuccess("Dados Alterados com sucesso.");

	header("Location: /profile");
	exit;
});

$app->get('/profile/orders', function(){

	User::verifyLogin(false);

	$user = User::getFromSession();
	$page = new Page();

	$page->setTpl('profile-orders',[
		"orders"=> $user->getOrders()
	]);
});

$app->get('/profile/orders/:idorder', function($idorder){

	User::verifyLogin(false);

	$order = new Order();
	$order->get((int)$idorder);

	$cart = new Cart();
	$cart->get((int)$order->getidcart());
	$cart->getCalculateTotal();
	
	$page = new Page();
	$page->setTpl('profile-orders-detail',[
		"order"=> $order->getValues(),
		"cart"=> $cart->getValues(),
		"products"=> $cart->getProducts()
	]);
});

?>