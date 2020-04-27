<?php 

namespace Ecommerce\PagSeguro;

use Exception;
use DOMDocument;
use DOMElement;
use Ecommerce\PagSeguro\Method\Method;

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
	private $sender;
	private $bank;
	
	public function __construct(
		Shipping $shipping,
		string $reference,
		Sender $sender,
		float $extraAmount = 0
	)
	{
		$this->sender = $sender;
		$this->extraAmount = number_format($extraAmount, 2, ".", "");
		$this->shipping = $shipping;
		$this->reference = $reference;
	}

	public function getDOMDocument():DOMDocument
	{

		$dom = new DOMDocument("1.0", "ISO-8859-1");

		return $dom;
	}

	public function addItem(Item $item)
	{
		array_push($this->items, $item);
	}

	public function setCreditCard(CreditCard $creditCard)
	{
		$this->creditCard = $creditCard;
		$this->method = Method::CREDIT_CARD;
	}

	public function setBank(Bank $bank)
	{
		$this->bank = $bank;
		$this->method = Method::DEBIT;
	}

	public function setBoleto()
	{
		$this->method = Method::BOLETO;
	}
}

?>