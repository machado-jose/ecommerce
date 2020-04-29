<?php 

namespace Ecommerce\PagSeguro;

use \GuzzleHttp\Client;
use \Ecommerce\Model\Order;

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

	public static function sendTransaction(Payment $payment)
	{
		$client = new Client();
		
		$res = $client->request('POST', Config::getUrlTransaction().'?'.http_build_query(Config::getAuthentication()), [
			"verify"=>false,
			"headers"=> [
				'Content-Type'=> 'application/xml'
			],
			"body"=> $payment->getDOMDocument()->saveXML()
		]);

		$xml = simplexml_load_string($res->getBody()->getContents());

		$order = new Order();
		$order->get((int)$xml->reference);
		$order->setPagseguroTransactionResponse(
			(string)$xml->code,
			(float)$xml->grossAmount,
			(float)$xml->discountAmount,
			(float)$xml->feeAmount,
			(float)$xml->netAmount,
			(float)$xml->extraAmount,
			(string)$xml->paymentLink
		);

		return $xml;
	}

	public static function getNotification(string $code, string $type)
	{

		switch ($type) {
			case 'transaction':
				$url = Config::getUrlNotification();
				break;
			
			default:
				throw new Exception("Notificação Inválida");	
				break;
		}

		$client = new Client();

		$res = $client->request('GET', $url. $code.'?'.http_build_query(Config::getAuthentication()), [
			"verify"=>false
		]);

		$xml = simplexml_load_string($res->getBody()->getContents());
		var_dump($xml);
		exit;
	}

}

?>