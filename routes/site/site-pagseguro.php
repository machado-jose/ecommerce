<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Order;
use \Ecommerce\Model\Cart;
use \Ecommerce\PagSeguro\Config;
use \Ecommerce\PagSeguro\Transporter;

$app->post('/payment/credit', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();

	$address = $order->getAddress();
	$cart = $order->getCart();

	var_dump($address->getValues());
	var_dump($cart->getValues());
	var_dump($order->getValues());
	exit;
});

$app->get('/payment', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();

	$years = [];
	for($y = date('Y'); $y < date('Y') + 14; $y++)
	{
		array_push($years, $y);
	}

	$page = new Page();
	$page->setTpl('payment',[
		"order"=> $order->getValues(),
		"msgError"=> Order::getMsgError(),
		"years"=> $years,
		"pagseguro"=> [
			"urlJS"=> Config::getUrlJS(),
			"id"=>  Transporter::createSession(),
			"maxInstallmentNoInterest"=> Config::MAX_INSTALLMENT_NO_INTEREST,
			"maxInstallment"=> Config::MAX_INSTALLMENT
		]
	]);

});

?>