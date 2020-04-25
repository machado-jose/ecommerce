<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\User;
use \Ecommerce\PagSeguro\Config;
use \GuzzleHttp\Client;


$app->get('/payment/pagseguro', function(){

	//User::verifyLogin(false);

	$client = new Client();
	$res = $client->request('POST', Config::getUrlSession().'?'.http_build_query(Config::getAuthentication()), [
		"verify"=>false
	]);

	echo $res->getBody()->getContents();
});

?>