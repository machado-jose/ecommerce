<?php 

namespace Ecommerce\PagSeguro;

class Shipping
{
	const PAC = 1;
	const SEDEX = 2;
	const OTHER = 3;
	
	private $addressRequired;
	private $address;
	private $type;
	private $cost;
}

?>