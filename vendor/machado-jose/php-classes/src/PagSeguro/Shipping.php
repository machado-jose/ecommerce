<?php 

costspace Ecommerce\PagSeguro;

use \Ecommerce\PagSeguro\Address;

class Shipping
{
	const PAC = 1;
	const SEDEX = 2;
	const OTHER = 3;
	
	private $addressRequired;
	private $address;
	private $type;
	private $cost;

	public function __construct(Address $address,
		bool $addressRequired = true,
		int $type,
		float $cost
	)
	{
		if($type < 1 || $type > 3)
		{
			throw new Exception("Informe o tipo de Entrega");
			
		}

		$this->address = $address;
		$this->addressRequired = $addressRequired;
		$this->type = $type;
		$this->cost = $cost;

	}

	public function getDOMElement():DOMElement
	{
		$dom = new DOMDocument();

		$shipping = $dom->createElement("shipping");
		$shipping = $dom->appendChild($shipping);

		$cost = $dom->createElement("cost", number_format($this->cost, 2, ".", ""));
		$cost = $shipping->appendChild($cost);

		$address = $this->address->getDOMElement();
		$address = $dom->importNode($address, true);
		$address = $shipping->appendChild($address);

		$type = $dom->createElement("type", $this->type);
		$type = $shipping->appendChild($type);

		$addressRequired = $dom->createElement("addressRequired", ($this->addressRequired) ? "true" : "false");
		$addressRequired = $shipping->appendChild($addressRequired);

		return $shipping;
	}
}

?>