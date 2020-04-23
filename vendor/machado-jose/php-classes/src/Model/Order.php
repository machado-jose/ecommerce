<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;
use \Ecommerce\Model\Cart;

class Order extends Model{

	const SESSION_ORDER_ERROR = "OrderError";
	const SESSION_ORDER_SUCCESS = "OrderSuccess";
	
	public function save()
	{
		$sql = new Sql();
		$results = $sql->select("CALL sp_orders_save(:idorder, :idcart, :iduser, :idstatus, :idaddress, :vltotal)", array(
			":idorder"=> $this->getidorder(),
			":idcart"=> $this->getidcart(),
			":iduser"=> $this->getiduser(),
			":idstatus"=> $this->getidstatus(),
			":idaddress"=> $this->getidaddress(),
			":vltotal"=> $this->getvltotal()
		));

		if(count($results) > 0)
		{
			$this->setDatas($results[0]);
		}
	}

	public function get($idorder)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT *
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING (idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.iduser
			WHERE a.idorder = :idorder",
			[
			":idorder"=>$idorder
		]);

		if(count($results) > 0)
		{
			$this->setDatas($results[0]);
		}
	}

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select("SELECT *
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING (idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.iduser
			ORDER BY a.dtregister DESC");
	}

	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM tb_orders WHERE idorder = :idorder", [
			":idorder"=> $this->getidorder()
		]);
	}

	public function getCart():Cart
	{
		$cart = new Cart();
		$cart->get($this->getidcart());
		return $cart;

	}

	public static function setMsgError($msg)
	{
		$_SESSION[Order::SESSION_ORDER_ERROR] = $msg;
	}

	public static function getMsgError()
	{
		$msg = (isset($_SESSION[Order::SESSION_ORDER_ERROR])) ? $_SESSION[Order::SESSION_ORDER_ERROR] : '';
		Order::clearMsgError();
		return $msg;
	}

	public static function clearMsgError()
	{
		$_SESSION[Order::SESSION_ORDER_ERROR] = NULL;
	}

	public static function setMsgSuccess($msg)
	{
		$_SESSION[Order::SESSION_ORDER_SUCCESS] = $msg;
	}

	public static function getMsgSuccess()
	{
		$msg = (isset($_SESSION[Order::SESSION_ORDER_SUCCESS])) ? $_SESSION[Order::SESSION_ORDER_SUCCESS] : '';
		Order::clearMsgSuccess();
		return $msg;
	}

	public static function clearMsgSuccess()
	{
		$_SESSION[Order::SESSION_ORDER_SUCCESS] = NULL;
	}

	public function updateStatus()
	{
		$sql = new Sql();
		$sql->query("UPDATE tb_orders SET idstatus = :idstatus WHERE idorder = :idorder", [
			":idstatus"=> $this->getidstatus(),
			":idorder"=> $this->getidorder()
		]);
	}

}

?>