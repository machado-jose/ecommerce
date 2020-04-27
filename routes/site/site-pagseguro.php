<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Order;
use \Ecommerce\Model\Cart;
use \Ecommerce\PagSeguro\Config;
use \Ecommerce\PagSeguro\Transporter;
use \Ecommerce\PagSeguro\Document;
use \Ecommerce\PagSeguro\Phone;
use \Ecommerce\PagSeguro\Address;
use \Ecommerce\PagSeguro\Sender;
use \Ecommerce\PagSeguro\Shipping;
use \Ecommerce\PagSeguro\CreditCard;
use \Ecommerce\PagSeguro\Item;
use \Ecommerce\PagSeguro\Payment;
use \Ecommerce\PagSeguro\CreditCard\Holder;
use \Ecommerce\PagSeguro\CreditCard\Installment;

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
			(float)$product['vlprice'],
			(int)$product['nrtotal']
		);

		$payment->addItem($item);
	}

	//Teste
	$dom = $payment->getDOMDocument();
	echo $dom->saveXML();
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