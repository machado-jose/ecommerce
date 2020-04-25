<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Order;
use \Ecommerce\PagSeguro\Config;
use \Ecommerce\PagSeguro\Transporter;

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
			"id"=>  Transporter::createSession()
		]
	]);

});

?>