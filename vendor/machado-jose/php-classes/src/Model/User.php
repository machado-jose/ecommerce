<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;

class User extends Model{

	const SESSION = "User";

	public function save()
	{
		$sql = new Sql();

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
			":pdespassword"=> $this->getdespassword(),
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

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson ");
	}
}

?>