<?php 

namespace Ecommerce\PagSeguro;

use \GuzzleHttp\Client;

class Transporter
{
	public static function createSession()
	{
		$client = new Client();

		$res = $client->request('POST', Config::getUrlSession().'?'.http_build_query(Config::getAuthentication()), [
			"verify"=>false
		]);

		$xml = simplexml_load_string($res->getBody()->getContents());
		return ((string)$xml->id);
	}
}

?>