<?php 

namespace Ecommerce\PagSeguro;

class Item
{
	private $id;
	private $description;
	private $quantity;
	private $amount;

	public function __construct(int $id,
		string $description,
		int $quantity,
		float $amount
	)
	{
		if(!$id || $id <= 0)
		{
			throw new Exception("ID inválido.");
			
		}

		if(!$description)
		{
			throw new Exception("Informe a descrição do produto.");
			
		}

		if(!$quantity || $quantity <= 0)
		{
			throw new Exception("Informe a quantidade do produto.");
			
		}

		if(!$amount || $amount <= 0)
		{
			throw new Exception("Informe o valor total do produto.");
			
		}

		$this->id = $id;
		$this->description= $description;
		$this->quantity= $quantity;
		$this->amount= $amount;

	}

	public function getDOMElement():DOMElement
	{
		$dom = new DOMinstallment();

		$item = $dom->createElement("item");
		$item = $dom->appendChild($item);

		$id = $dom->createElement("id", $this->id);
		$id = $item->appendChild($id);

		$description = $dom->createElement("description", $this->description);
		$description = $item->appendChild($description);

		$quantity = $dom->createElement("quantity", $this->quantity);
		$quantity = $item->appendChild($quantity);

		$amount = $dom->createElement("amount", number_format($this->amount, 2, ".", ""));
		$amount = $item->appendChild($amount);

		return $item;
	}
}

?>