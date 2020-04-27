<?php 

namespace Ecommerce\PagSeguro;

use Exception;
use DOMDocument;
use DOMElement;

class Sender
{
	private $name;
	private $email;
	private $phone;
	private $document;
	private $hash;

	public function __construct(string $name,
		string $email,
		Phone $phone,
		Document $document,
		string $hash
	)
	{
		if(!$name)
		{
			throw new Exception("Informe o nome do comprador.");	
		}

		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			throw new Exception("O email informado é inválido.");	
		}

		if(!$hash)
		{
			throw new Exception("Informe o hash.");	
		}

		$this->name = $name;
		$this->email = $email;
		$this->phone = $phone;
		$this->document = $document;
		$this->hash = $hash;

	}

	public function getDOMElement():DOMElement
	{
		$dom = new DOMDocument();

		$sender = $dom->createElement("sender");
		$sender = $dom->appendChild($sender);

		$name = $dom->createElement("name", $this->name);
		$name = $sender->appendChild($name);

		$email = $dom->createElement("email", $this->email);
		$email = $sender->appendChild($email);

		$phone = $this->phone->getDOMElement();
		$phone = $dom->importNode($phone, true);
		$phone = $sender->appendChild($phone);

		$documents = $dom->createElement("documents");
		$documents = $sender->appendChild($documents);

		$document = $this->document->getDOMElement();
		// O parâmetro true significa que irá importar também os nós filhos
		$document = $dom->importNode($document, true);
		$document = $documents->appendChild($document);

		$hash = $dom->createElement("hash", $this->hash);
		$hash = $sender->appendChild($hash);

		return $sender;
	}
	
}

?>