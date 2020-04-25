<?php 

namespace Ecommerce\PagSeguro;

class Config
{
	const SANDBOX = true;

	const SANDBOX_EMAIL = "***@***.com";
	const PRODUCTION_EMAIL = "***@***.com";

	const SANDBOX_TOKEN = "****";
	// ****** This token is private ******
	const PRODUCTION_TOKEN = "****";

	const SANDBOX_SESSION = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";
	const PRODUCTION_SESSION= "https://ws.pagseguro.uol.com.br/v2/sessions";

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
}

?>