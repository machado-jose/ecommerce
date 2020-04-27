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
	private $notificationURL = Config::NOTIFICATION_URL;
	private $items = [];
	//Posso cobrar taxa extra ou oferecer desconto (valor negativo)
	private $extraAmount = "0.00";
	private $reference;
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

		$payment = $dom->createElement("payment");
		$payment = $dom->appendChild($payment);

		$mode = $dom->createElement("mode", $this->mode);
		$mode = $payment->appendChild($mode);

		$method = $dom->createElement("method", $this->method);
		$method = $payment->appendChild($method);

		if($this->method === Method::DEBIT) {
			
			$bank = $this->bank->getDOMElement();
			$bank = $dom->importNode($bank, true);
			$bank = $payment->appendChild($bank);
				
		}

		$sender = $this->sender->getDOMElement();
		$sender = $dom->importNode($sender, true);
		$sender = $payment->appendChild($sender);

		$currency = $dom->createElement("currency", $this->currency);
		$currency = $payment->appendChild($currency);

		$notificationURL = $dom->createElement("notificationURL", $this->notificationURL);
		$notificationURL = $payment->appendChild($notificationURL);

		$items = $dom->createElement("items");
		$items = $payment->appendChild($items);

		foreach ($this->items as $_item) {
			$item = $_item->getDOMElement();
			$item = $dom->importNode($item, true);
			$item = $items->appendChild($item);
		}

		$extraAmount = $dom->createElement("extraAmount", $this->extraAmount);
		$extraAmount = $payment->appendChild($extraAmount);

		$reference = $dom->createElement("reference", $this->reference);
		$reference = $payment->appendChild($reference);

		$shipping = $this->shipping->getDOMElement();
		$shipping = $dom->importNode($shipping, true);
		$shipping = $payment->appendChild($shipping);

		if($this->method === Method::CREDIT_CARD) {
			
			$creditCard = $this->creditCard->getDOMElement();
			$creditCard = $dom->importNode($creditCard, true);
			$creditCard = $payment->appendChild($creditCard);
				
		}

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