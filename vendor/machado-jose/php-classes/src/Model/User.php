<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;

class User extends Model{

	const SESSION = "User";

	public static function login($deslogin, $despassword)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", [":LOGIN"=> $deslogin]);

		if(count($results) === 0 ) throw new \Exception("Usuário não encontrado ou Senha Inválida");

		$datas = $results[0];
		if(password_verify($despassword, $datas['despassword']) === true){

			$user = new User();
			$user->setDatas($datas);
			$_SESSION[User::SESSION] = $user->getValues();
			return $user;

		}else{
			throw new \Exception("Usuário não encontrado ou Senha Inválida");
		}
		
	}

	public static function verifyLogin($inadmin = true)
	{

		if(
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]['iduser'] > 0
			||
			(bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin
		){
			header("Location: /admin/login");
			exit;
		}
	}

	public static function logout()
	{
		unset($_SESSION[User::SESSION]);
	}
}

?>