<?php 

namespace Ecommerce\PagSeguro\CreditCard;

use Exception;
use DOMDocument;
use DOMElement;
use DateTime;
use \Ecommerce\PagSeguro\Phone;
use \Ecommerce\PagSeguro\Document;

class Holder
{

	private $name;
	private $document;
	private $birthDate;
	private $phone;

	public function __construct(string $name,
		Phone $phone,
		Document $document,
		DateTime $birthDate
	)
	{
		if(!$name)
		{
			throw new Exception("Informe o nome do comprador.");	
		}

		$this->name = $name;
		$this->birthDate = $birthDate;
		$this->phone = $phone;
		$this->document = $document;

	}

	public function getDOMElement():DOMElement
	{
		$dom = new DOMDocument();

		$holder = $dom->createElement("holder");
		$holder = $dom->appendChild($holder);

		$name = $dom->createElement("name", $this->name);
		$name = $holder->appendChild($name);

		$documents = $dom->createElement("documents");
		$documents = $holder->appendChild($documents);

		$document = $this->document->getDOMElement();
		$document = $dom->importNode($document, true);
		$document = $documents->appendChild($document);

		$birthDate = $dom->createElement("birthDate", $this->birthDate->format('d/m/Y'));
		$birthDate= $holder->appendChild($birthDate);

		$phone = $this->phone->getDOMElement();
		$phone = $dom->importNode($phone, true);
		$phone = $holder->appendChild($phone);

		return $holder;
	}
	
}

?>