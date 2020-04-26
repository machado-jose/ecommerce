<?php 

namespace Ecommerce\PagSeguro;

class Payment
{
	private $mode = "default";
	private $method;
	private $currency = "BRL";
	private $notificationURL = "http://e-commerce.com.br/payment/notification";
	private $items = [];
	//Posso cobrar taxa extra ou oferecer desconto (valor negativo)
	private $extraAmount = "0.00";
	private $reference = "";
	private $shipping;
	private $creditCard;
	
}

?>