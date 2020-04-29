<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\Cart;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Address;
use \Ecommerce\Model\Order;
use \Ecommerce\Model\OrderStatus;

$app->get('/checkout', function(){

	User::verifyLogin(false);

	try
	{
		$address = new Address();
		$cart = Cart::getFromSession();
		
		if(!(bool)$cart->getdeszipcode())
		{
			$user = User::getFromSession();
			$address->getFromPerson($user->getidperson());
			$cart->setdeszipcode($address->getdeszipcode());
		}
		else
		{
			$address->loadFromCep($cart->getdeszipcode());
		}

		$page = new Page();
		$page->setTpl('checkout', array(
			"cart"=> $cart->getValues(),
			"address"=> $address->getValues(),
			"products"=> $cart->getProducts(),
			"error"=> User::getMsgError(User::SESSION_REGISTER_ERROR)
		));
	}
	catch(\Exception $e)
	{
		Address::setMsgError("Código ".$e->getCode().": ".$e->getMessage());
		header("Location: /error");
		exit;
	}
});

$app->post('/checkout/change-cep', function(){

	User::verifyLogin(false);
	$address = new Address();
	$cart = Cart::getFromSession();

	$address->loadFromCep($_POST["zipcode"]);
	$cart->setdeszipcode($_POST["zipcode"]);
	$cart->updateZipcode();
	$cart->getCalculateTotal();

	header("Location: /checkout");
	exit;
});

$app->post('/checkout', function(){

	User::verifyLogin(false);
	
	$address = new Address();
	$user = User::getFromSession();

	if(!isset($_POST['zipcode']) || $_POST['zipcode'] === '')
	{
		User::setMsgError("Informe o CEP.", User::SESSION_REGISTER_ERROR);
		header("Location: /checkout");
		exit;
	}

	if(!isset($_POST['desaddress']) || $_POST['desaddress'] === '')
	{
		User::setMsgError("Informe o Endereço.", User::SESSION_REGISTER_ERROR);
		header("Location: /checkout");
		exit;
	}

	if(!isset($_POST['desnumber']) || $_POST['desnumber'] === '')
	{
		User::setMsgError("Informe o número.", User::SESSION_REGISTER_ERROR);
		header("Location: /checkout");
		exit;
	}

	if(!isset($_POST['desdistrict']) || $_POST['desdistrict'] === '')
	{
		User::setMsgError("Informe o Bairro.", User::SESSION_REGISTER_ERROR);
		header("Location: /checkout");
		exit;
	}

	if(!isset($_POST['descity']) || $_POST['descity'] === '')
	{
		User::setMsgError("Informe a Cidade.", User::SESSION_REGISTER_ERROR);
		header("Location: /checkout");
		exit;
	}

	if(!isset($_POST['desstate']) || $_POST['desstate'] === '')
	{
		User::setMsgError("Informe o Estado.", User::SESSION_REGISTER_ERROR);
		header("Location: /checkout");
		exit;
	}

	if(!isset($_POST['descountry']) || $_POST['descountry'] === '')
	{
		User::setMsgError("Informe o País.", User::SESSION_REGISTER_ERROR);
		header("Location: /checkout");
		exit;
	}

	$_POST['deszipcode'] = $_POST['zipcode'];
	$_POST['idperson'] = $user->getidperson();

	$address->setDatas($_POST);
	if(!$address->verifyAddressExists()) $address->save();

	$order = new Order();
	$cart = Cart::getFromSession();
	$cart->getCalculateTotal();

	$order->setDatas([
		"idcart"=> $cart->getidcart(),
		"iduser"=> $user->getiduser(),
		"idstatus"=> OrderStatus::EM_ABERTO,
		"idaddress"=> $address->getidaddress(),
		"vltotal"=> $cart->getvltotal()
	]);

	$order->save();

	$order->toSession();

	header("Location: /payment");
	exit;
});

?>