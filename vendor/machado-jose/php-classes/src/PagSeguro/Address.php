<?php 

namespace Ecommerce\PagSeguro;

class Address
{

	private $street;
	private $number;
	private $district;
	private $city;
	private $state;
	private $country;
	private $postalCode;

	public function __construct(string $street,
		string $number,
		string $district,
		string $city,
		string $state,
		string $country,
		string $postalCode
	)
	{
		if(!$street)
		{
			throw new Exception("Informe o logradouro do endereço.");	
		}

		if(!$number)
		{
			throw new Exception("Informe o número.");	
		}

		if(!$district)
		{
			throw new Exception("Informe o bairro.");
		}

		if(!$city)
		{
			throw new Exception("Informe a cidade.");	
		}

		if(!$state)
		{
			throw new Exception("Informe o estado.");	
		}

		if(!$country)
		{
			throw new Exception("Informe o país.");	
		}

		if(!$postalCode)
		{
			throw new Exception("Informe o CEP.");	
		}

		$this->street = $street;
		$this->number = $number;
		$this->district = $district;
		$this->city = $city;
		$this->state = $state;
		$this->country = $country;
		$this->postalCode = $postalCode;

	}

	public function getDOMElement($node = "address"):DOMElement
	{
		$dom = new DOMDocument();

		$address = $dom->createElement($node);
		$address = $dom->appendChild($address);

		$street = $dom->createElement("street", $this->street);
		$street = $address->appendChild($street);

		$number = $dom->createElement("number", $this->number);
		$number = $address->appendChild($number);

		$district = $dom->createElement("district", $this->district);
		$district = $address->appendChild($district);

		$city = $dom->createElement("city", $this->city);
		$city = $address->appendChild($city);

		$state = $dom->createElement("state", $this->state);
		$state = $address->appendChild($state);

		$country = $dom->createElement("country", $this->country);
		$country = $address->appendChild($country);

		$postalCode = $dom->createElement("postalCode", $this->postalCode);
		$postalCode = $address->appendChild($postalCode);

		return $address;
	}
}

?>