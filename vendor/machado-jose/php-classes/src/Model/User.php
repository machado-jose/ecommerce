<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;
use \Ecommerce\Mailer;

class User extends Model{

	const SESSION = "User";
	const PASSWORD = "senha";
	const SESSION_ERROR = "UserError";

	public function save()
	{
		$sql = new Sql();

		$passwordCrypt = User::cryptPassword($this->getdespassword());

		$results = $sql->select("CALL sp_users_save(
			:pdesperson,
			:pdeslogin,
			:pdespassword,
			:pdesemail,
			:pnrphone,
			:pinadmin
		)", array(
			":pdesperson"=> $this->getdesperson(),
			":pdeslogin"=> $this->getdeslogin(),
			":pdespassword"=> $passwordCrypt,
			":pdesemail"=> $this->getdesemail(),
			":pnrphone"=> $this->getnrphone(),
			":pinadmin"=> $this->getinadmin()
		));

		$this->setDatas($results[0]);

	}

	public function get($iduser)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser ORDER BY b.desperson ", array(":iduser"=> $iduser));
		$this->setDatas($results[0]);
	}

	public static function getFromSession()
	{
		$user = new User();
		if(isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0)
		{
			$user->setDatas($_SESSION[User::SESSION]);
		}

		return $user;
	}

	public static function checkLogin($inadmin = true)
	{
		if(
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]['iduser'] > 0
		){
			// Não está logado
			return false;
		}
		else
		{
			if($inadmin && (bool)$_SESSION[User::SESSION]['inadmin']){
				return true;
			}
			else if(!$inadmin)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public function update()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(
			:piduser,
			:pdesperson,
			:pdeslogin,
			:pdespassword,
			:pdesemail,
			:pnrphone,
			:pinadmin
		)", array(
			":piduser"=> $this->getiduser(),
			":pdesperson"=> $this->getdesperson(),
			":pdeslogin"=> $this->getdeslogin(),
			":pdespassword"=> $this->getdespassword(),
			":pdesemail"=> $this->getdesemail(),
			":pnrphone"=> $this->getnrphone(),
			":pinadmin"=> $this->getinadmin()
		));

		$this->setDatas($results[0]);
	}

	public function delete()
	{
		$sql = new Sql();
		$sql->query("CALL sp_users_delete(:piduser)", array(
			":piduser"=> $this->getiduser()
		));
	}

	public function getForgot($email)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b
			USING(idperson)
			WHERE a.desemail = :email", array(":email"=>$email));

		if(count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");			
		}
		else
		{
			$data = $results[0];
			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:piduser, :pdesip)", array(
					":piduser"=>$data['iduser'],
					":pdesip"=>$_SERVER['REMOTE_ADDR']
			));

			if(count($results2) === 0)
			{
				throw new \Exception("Não foi possível recuperar a senha.");
			}
			else
			{

				$code = User::cryptData(json_encode($results2[0]["idrecovery"]));

				$link = "http://e-commerce.com.br/admin/forgot/reset/$code";

				$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir Senha", 'forgot', array(
					"name"=>$data['desperson'],
					"link"=> $link
				));

				$mailer->send();

				return $data;
			}
		}
	}

	private static function cryptData($data)
	{
		if($data != null)
		{
			define('SECRET', pack('a16', User::PASSWORD));
			define('SECRET_IV', pack('a16', User::PASSWORD));

			return base64_encode(openssl_encrypt(
		
				$data,
				'AES-128-CBC',
				SECRET,
				0,
				SECRET_IV

			));
		}
		return null;	
	}

	private static function descryptData($cryptData)
	{		
		if($cryptData != null)
		{
			define('SECRET', pack('a16', User::PASSWORD));
			define('SECRET_IV', pack('a16', User::PASSWORD));

			return openssl_decrypt(
				$cryptData, 
				'AES-128-CBC', 
				SECRET, 
				0, 
				SECRET_IV
			);
		}
		return null;
	}

	public static function cryptPassword($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, ["cost"=>12]);
	}

	public function setPassword($password)
	{
		$sql = new Sql();
		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
			":password"=> $password,
			":iduser"=> $this->getiduser()
		));
	}

	private static function createSession($datas)
	{
		$user = new User();
		$user->setDatas($datas);
		$_SESSION[User::SESSION] = $user->getValues();
		return $user;
	}

	public static function validForgotDescrypt($code)
	{
		$idrecovery = json_decode(User::descryptData(base64_decode($code)));
		$sql = new Sql();
		$results = $sql->select("SELECT *
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE
				a.idrecovery = :idrecovery
			    AND
			    a.dtrecovery IS NULL
			    AND
			    DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()", array(
			":idrecovery"=>$idrecovery
		));
		if(count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{
			return $results[0];
		}
	}

	public static function setForgotUsed($idrecovery)
	{
		$sql = new Sql();
		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(":idrecovery"=>$idrecovery));
	}

	public static function login($deslogin, $despassword)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", [":LOGIN"=> $deslogin]);

		if(count($results) === 0 ) throw new \Exception("Usuário não encontrado ou Senha Inválida");

		$datas = $results[0];

		if(password_verify($despassword, $datas['despassword']))
		{
			User::createSession($datas);
		}else
		{
			throw new \Exception("Usuário não encontrado ou Senha Inválida");
		}
		
	}

	public static function loginToEmail($desemail, $despassword)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :desemail", [":desemail"=> $desemail]);

		if(count($results) === 0 ) throw new \Exception("Usuário não encontrado ou Senha Inválida");

		$datas = $results[0];

		if(password_verify($despassword, $datas['despassword']))
		{
			User::createSession($datas);
		}else
		{
			throw new \Exception("Usuário não encontrado ou Senha Inválida");
		}
		
	}

	public static function verifyLogin($inadmin = true)
	{

		if(!User::checkLogin($inadmin))
		{
			if($inadmin)
			{
				header("Location: /admin/login");
			}
			else
			{
				header("Location: /login");
			}
			
			exit;
		}
	}

	public static function logout()
	{
		unset($_SESSION[User::SESSION]);
	}

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson ");
	}

	public static function setMsgError($msg)
	{
		$_SESSION[User::SESSION_ERROR] = $msg;
	}

	public static function getMsgError()
	{
		$msg = (isset($_SESSION[User::SESSION_ERROR])) ? $_SESSION[User::SESSION_ERROR] : '';
		User::clearMsgError();
		return $msg;
	}

	public static function clearMsgError()
	{
		$_SESSION[User::SESSION_ERROR] = NULL;
	}

}

?>