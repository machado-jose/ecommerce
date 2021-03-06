<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;
use \Ecommerce\Model\Cart;
use \Ecommerce\Model\Address;

class Order extends Model{

	const SESSION_ORDER = "Order";
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
		$results = $sql->select("SELECT 
			a.idorder, a.idcart, a.iduser, a.idstatus, a.idaddress, a.vltotal, a.dtregister,
			b.desstatus,
			c.dessessionid, c.deszipcode, c.vlfreight, c.nrdays,
			d.idperson, d.deslogin,
			e.desaddress, e.desnumber, e.descomplement, e.descity, e.desstate, e.descountry, e.deszipcode, e.desdistrict,
			f.desperson, f.desemail, f.nrphone,
			g.descode, g.vlgrossamount, g.vldiscountamount, g.vlfeeamount, g.vlnetamount, g.vlextraamount, g.despaymentlink
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING (idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.iduser
			LEFT JOIN tb_orderspagseguro g ON g.idorder = a.idorder
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

	public static function getOrdersPage($page = 1, $itemsPerPage = 10)
	{
		$sql = new Sql();
		$start = ($page - 1) * $itemsPerPage;

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING (idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.iduser
			ORDER BY a.dtregister DESC
			LIMIT $start, $itemsPerPage");

		$resultsTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal");
		return [
			'data'=> $results,
			'total'=> (int)$resultsTotal[0]['nrtotal'],
			'pages'=> (int)ceil($resultsTotal[0]['nrtotal'] / $itemsPerPage)
		]; 
	}

	public static function getOrdersPageSearch($search, $page = 1, $itemsPerPage = 10)
	{
		$sql = new Sql();
		$start = ($page - 1) * $itemsPerPage;

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING (idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.iduser
			WHERE a.idorder = :id OR f.desperson LIKE :search
			ORDER BY a.dtregister DESC
			LIMIT $start, $itemsPerPage", [
				":search"=> '%'.$search.'%',
				":id"=> $search
			]);

		$resultsTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal");
		return [
			'data'=> $results,
			'total'=> (int)$resultsTotal[0]['nrtotal'],
			'pages'=> (int)ceil($resultsTotal[0]['nrtotal'] / $itemsPerPage)
		]; 
	}

	public function toSession()
	{
		$_SESSION[Order::SESSION_ORDER] = $this->getValues();
	}

	public function getFromSession()
	{
		$this->setDatas($_SESSION[Order::SESSION_ORDER]);
	}

	public function getAddress():Address
	{
		$address = new Address();
		$address->setDatas($this->getValues());
		return $address;
	}

	public function setPagseguroTransactionResponse(
		string $descode,
		float $vlgrossamount,
		float $vldiscountamount,
		float $vlfeeamount,
		float $vlnetamount,
		float $vlextraamount,
		string $despaymentlink = ""
	)
	{
		$sql = new Sql();
		$sql->query("CALL sp_orderspagseguro_save(
			:idorder,
			:descode,
			:vlgrossamount,
			:vldiscountamount,
			:vlfeeamount,
			:vlnetamount,
			:vlextraamount,
			:despaymentlink
		)",[
			":idorder"=> $this->getidorder(),
			":descode"=> $descode,
			":vlgrossamount"=> $vlgrossamount,
			":vldiscountamount"=> $vldiscountamount,
			":vlfeeamount"=> $vlfeeamount,
			":vlnetamount"=> $vlnetamount,
			":vlextraamount"=> $vlextraamount,
			":despaymentlink"=> $despaymentlink
		]);
	}

}

?>