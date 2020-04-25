<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Order;
use \Ecommerce\PagSeguro\Config;
use \GuzzleHttp\Client;

$app->get('/payment', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();

	$years = [];
	for($y = date('Y'); $y < date('Y') + 14; $y++)
	{
		array_push($years, $y);
	}

	$page = new Page(["footer"=>false]);
	$page->setTpl('payment',[
		"order"=> $order->getValues(),
		"msgError"=> Order::getMsgError(),
		"years"=> $years,
		"pagseguro"=> [
			"urlJS"=> Config::getUrlJS()
		]
	]);

});

$app->get('/payment/pagseguro', function(){

	$client = new Client();
	$res = $client->request('POST', Config::getUrlSession().'?'.http_build_query(Config::getAuthentication()), [
		"verify"=>false
	]);

	echo $res->getBody()->getContents();
});

?>