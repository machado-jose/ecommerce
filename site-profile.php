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

$app->get('/profile/change-password', function(){
	User::verifyLogin(false);

	$page = new Page();
	$page->setTpl('profile-change-password', [
		'changePassError'=> User::getMsgError(User::SESSION_REGISTER_ERROR),
		'changePassSuccess'=> User::getMsgSuccess()
	]);
});

$app->post('/profile/change-password', function(){

	User::verifyLogin(false);

	if(!isset($_POST['current_pass']) || $_POST['current_pass'] === '')
	{
		User::setMsgError("Digite a senha atual.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile/change-password");
			exit;
	}

	if(!isset($_POST['new_pass']) || $_POST['new_pass'] === '')
	{
		User::setMsgError("Digite a nova senha.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile/change-password");
			exit;
	}

	if(!isset($_POST['new_pass_confirm']) || $_POST['new_pass_confirm'] === '')
	{
		User::setMsgError("Confirme a nova senha.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile/change-password");
			exit;
	}

	if($_POST['new_pass'] === $_POST['current_pass'])
	{
		User::setMsgError("A sua nova senha deve ser diferente da atual.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile/change-password");
			exit;
	}

	$user = User::getFromSession();

	if(!password_verify($_POST['current_pass'], $user->getdespassword()))
	{
		User::setMsgError("A sua senha está inválida.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile/change-password");
			exit;
	}

	$user->setdespassword($_POST['new_pass']);
	$user->update();
	User::setMsgSuccess("A sua senha foi atualizada.");

	header("Location: /profile/change-password");
	exit;
});

?>