<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;
use \Ecommerce\Model\User;

class Cart extends Model
{

	const SESSION = "Cart";

	public static function getFromSession()
	{
		$cart = new Cart();
		if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0){
			$cart->get($_SESSION[Cart::SESSION]['idcart']);
		}
		else
		{
			$cart->getFromSessionID();

			if(!(int)$cart->getidcart() > 0)
			{
				$data = [
					"dessessionid"=> session_id()
				];

				if(User::checkLogin(false))
				{
					$user = User::getFromSession();
					$data['iduser'] = $user->getiduser();
				}

				$cart->setDatas($data);
				$cart->save();
				$cart->setToSession();
			}
		}

		return $cart;
	}

	public function setToSession()
	{
		$_SESSION[Cart::SESSION] = $this->getValues();
	}

	public function getFromSessionID()
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [":dessessionid"=>session_id()]);
		if(count($results) > 0) $this->setDatas($results[0]);
	}

	public function get($idcart)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [":idcart"=>$idcart]);
		if(count($results) > 0) $this->setDatas($results[0]);
	}

	public function save()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_carts_save(
			:idcart,
			:dessessionid,
			:iduser,
			:deszipcode,
			:vlfreight,
			:nrday
		)", array(
			":idcart"=> $this->getidcart(),
			":dessessionid"=> $this->getdessessionid(),
			":iduser"=> $this->getiduser(),
			":deszipcode"=> $this->getdeszipcode(),
			":vlfreight"=> $this->getvlfreight(),
			":nrday"=> $this->getnrday()
		));

		$this->setDatas($results[0]);

	}

}
?>