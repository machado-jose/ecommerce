<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Order;
use \Ecommerce\Model\Cart;
use \Ecommerce\PagSeguro\Config;
use \Ecommerce\PagSeguro\Transporter;
use \Ecommerce\PagSeguro\Document;
use \Ecommerce\PagSeguro\Phone;
use \Ecommerce\PagSeguro\Bank;
use \Ecommerce\PagSeguro\Address;
use \Ecommerce\PagSeguro\Sender;
use \Ecommerce\PagSeguro\Shipping;
use \Ecommerce\PagSeguro\CreditCard;
use \Ecommerce\PagSeguro\Item;
use \Ecommerce\PagSeguro\Payment;
use \Ecommerce\PagSeguro\CreditCard\Holder;
use \Ecommerce\PagSeguro\CreditCard\Installment;

$app->get('/payment/success/boleto', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();

	$page = new Page();
	$page->setTpl('payment-success-boleto',[
		"order"=> $order->getValues()
	]);
});

$app->get('/payment/success/debit', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();

	$page = new Page();
	$page->setTpl('payment-success-debit',[
		"order"=> $order->getValues()
	]);
});

$app->post('/payment/boleto', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();
	$order->get((int)$order->getidorder());

	$address = $order->getAddress();
	$cart = $order->getCart();

	$birthday = new DateTime($_POST['birth']);

	$phone = new Phone($_POST['ddd'], $_POST['phone']);

	$document = new Document(Document::CPF, $_POST['cpf']);

	$shippingAddress = new Address(
		$address->getdesaddress(),
		$address->getdesnumber(),
		$address->getdesdistrict(),
		$address->getdescomplement(),
		$address->getdescity(),
		$address->getdesstate(),
		$address->getdescountry(),
		$address->getdeszipcode()
	);

	$sender = new Sender(
		$order->getdesperson(),
		$order->getdesemail(),
		$phone,
		$document,
		$_POST['hash']
	);

	$shipping = new Shipping(
		$shippingAddress,
		Shipping::PAC,
		(float)$cart->getvlfreight()
	);

	$payment = new Payment(
		$shipping,
		(string)$order->getidorder(),
		$sender
	);

	foreach ($cart->getProducts() as $product) {
		
		$item = new Item(
			(int)$product['idproduct'],
			$product['desproduct'],
			(int)$product['nrtotal'],
			(float)$product['vlprice']
		);

		$payment->addItem($item);
	}

	$payment->setBoleto();

	Transporter::sendTransaction($payment);
	echo json_encode(["success"=>true]);

});

$app->get('/payment/success', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();

	$page = new Page();
	$page->setTpl('payment-success',[
		"order"=> $order->getValues()
	]);
});

$app->post('/payment/credit', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();
	$order->get((int)$order->getidorder());

	$address = $order->getAddress();
	$cart = $order->getCart();

	$birthday = new DateTime($_POST['birth']);

	$phone = new Phone($_POST['ddd'], $_POST['phone']);

	$document = new Document(Document::CPF, $_POST['cpf']);

	$shippingAddress = new Address(
		$address->getdesaddress(),
		$address->getdesnumber(),
		$address->getdesdistrict(),
		$address->getdescomplement(),
		$address->getdescity(),
		$address->getdesstate(),
		$address->getdescountry(),
		$address->getdeszipcode()
	);

	$sender = new Sender(
		$order->getdesperson(),
		$order->getdesemail(),
		$phone,
		$document,
		$_POST['hash']
	);

	$holder = new Holder(
		$_POST['name'],
		$phone,
		$document,
		$birthday
	);

	$shipping = new Shipping(
		$shippingAddress,
		Shipping::PAC,
		(float)$cart->getvlfreight()
	);

	$installment = new Installment(
		(int)$_POST['installments_qtd'],
		(float)$_POST['installments_value']
	);

	$billingAddress = new Address(
		$address->getdesaddress(),
		$address->getdesnumber(),
		$address->getdesdistrict(),
		$address->getdescomplement(),
		$address->getdescity(),
		$address->getdesstate(),
		$address->getdescountry(),
		$address->getdeszipcode()
	);

	$creditCard = new CreditCard(
		$_POST['token'],
		$installment,
		$holder,
		$billingAddress
	);

	$payment = new Payment(
		$shipping,
		(string)$order->getidorder(),
		$sender
	);

	foreach ($cart->getProducts() as $product) {
		
		$item = new Item(
			(int)$product['idproduct'],
			$product['desproduct'],
			(int)$product['nrtotal'],
			(float)$product['vlprice']
		);

		$payment->addItem($item);
	}

	$payment->setCreditCard($creditCard);

	Transporter::sendTransaction($payment);
	echo json_encode(["success"=>true]);
	
});

$app->post('/payment/debit', function(){

	User::verifyLogin(false);

	$order = new Order();
	$order->getFromSession();
	$order->get((int)$order->getidorder());

	$address = $order->getAddress();
	$cart = $order->getCart();

	$birthday = new DateTime($_POST['birth']);

	$phone = new Phone($_POST['ddd'], $_POST['phone']);

	$document = new Document(Document::CPF, $_POST['cpf']);

	$shippingAddress = new Address(
		$address->getdesaddress(),
		$address->getdesnumber(),
		$address->getdesdistrict(),
		$address->getdescomplement(),
		$address->getdescity(),
		$address->getdesstate(),
		$address->getdescountry(),
		$address->getdeszipcode()
	);

	$sender = new Sender(
		$order->getdesperson(),
		$order->getdesemail(),
		$phone,
		$document,
		$_POST['hash']
	);

	$shipping = new Shipping(
		$shippingAddress,
		Shipping::PAC,
		(float)$cart->getvlfreight()
	);

	$payment = new Payment(
		$shipping,
		(string)$order->getidorder(),
		$sender
	);

	foreach ($cart->getProducts() as $product) {
		
		$item = new Item(
			(int)$product['idproduct'],
			$product['desproduct'],
			(int)$product['nrtotal'],
			(float)$product['vlprice']
		);

		$payment->addItem($item);
	}

	$bank = new Bank($_POST['bank']);

	$payment->setBank($bank);

	Transporter::sendTransaction($payment);
	echo json_encode(["success"=>true]);

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