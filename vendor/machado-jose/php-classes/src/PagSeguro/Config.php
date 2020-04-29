<?php 

namespace Ecommerce\PagSeguro;

class Config
{
	const SANDBOX = true;

	const SANDBOX_EMAIL = "*";
	const PRODUCTION_EMAIL = "*";

	const SANDBOX_TOKEN = "*";
	// ****** This token is private ******
	const PRODUCTION_TOKEN = "*";


	const SANDBOX_SESSION = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";
	const PRODUCTION_SESSION= "https://ws.pagseguro.uol.com.br/v2/sessions";

	const SANDBOX_URL_JS = "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";
	const PRODUCTION_URL_JS = "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";

	const MAX_INSTALLMENT_NO_INTEREST = 6;
	const MAX_INSTALLMENT = 10;

	const SANDBOX_URL_TRANSACTION = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions";
	const PRODUCTION_URL_TRANSACTION = "https://ws.pagseguro.uol.com.br/v2/transactions";

	const NOTIFICATION_URL = "http://e-commerce.com.br/payment/notification";

	const SANDBOX_URL_NOTIFICATION = "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/";
	const PRODUCTION_URL_NOTIFICATION = "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/";

	public static function getAuthentication():array
	{

		if(Config::SANDBOX === true)
		{
			return [
				"email"=> Config::SANDBOX_EMAIL,
				"token"=> Config::SANDBOX_TOKEN
			];
		}
		else
		{
			return [
				"email"=> Config::PRODUCTION_EMAIL,
				"token"=> Config::PRODUCTION_TOKEN
			];
		}

	}

	public static function getUrlSession():string
	{
		return (Config::SANDBOX) ? Config::SANDBOX_SESSION : Config::PRODUCTION_SESSION;
	}

	public static function getUrlJS():string
	{
		return (Config::SANDBOX) ? Config::SANDBOX_URL_JS : Config::PRODUCTION_URL_JS;
	}

	public static function getUrlTransaction():string
	{
		return (Config::SANDBOX) ? Config::SANDBOX_URL_TRANSACTION : Config::PRODUCTION_URL_TRANSACTION;
	}

	public static function getUrlNotification():string
	{
		return (Config::SANDBOX) ? Config::SANDBOX_URL_NOTIFICATION : Config::PRODUCTION_URL_NOTIFICATION;
	}
}

?>