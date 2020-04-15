<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;
use \Ecommerce\Mailer;

class User extends Model{

	const SESSION = "User";

	public function save()
	{
		$sql = new Sql();

		$passwordCrypt = User::cryptData($this->getdespassword());

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

				$code = User::cryptData(json_encode($results2["idrecovery"]));

				$link = "http://e-commerce.com.br/admin/forgot/reset?code=$code";

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
			define('SECRET', pack('a16', 'senha'));
			define('SECRET_IV', pack('a16', 'senha'));

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
			define('SECRET', pack('a16', 'senha'));
			define('SECRET_IV', pack('a16', 'senha'));

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

	private static function createSession($datas)
	{
		$user = new User();
		$user->setDatas($datas);
		$_SESSION[User::SESSION] = $user->getValues();
		return $user;
	}

	public static function login($deslogin, $despassword)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", [":LOGIN"=> $deslogin]);

		if(count($results) === 0 ) throw new \Exception("Usuário não encontrado ou Senha Inválida");

		$datas = $results[0];

		$passwordDescrypt = User::descryptData(base64_decode($datas['despassword']));

		if($passwordDescrypt === $despassword){
			User::createSession($datas);
		}
		// Essa condição foi escrita por causa do método de criptação armazenada no BD que hoje foi depreciada.
		else if(password_verify($despassword, $datas['despassword']))
		{
			User::createSession($datas);
		}else
		{
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

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson ");
	}
}

?>