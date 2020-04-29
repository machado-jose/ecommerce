<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Product;

class Cart extends Model
{

	const SESSION = "Cart";
	const SESSION_ERROR = "CartError";
	const LIMIT_MIN_WEIGHT_FREIGHT = 0.3;
	const LIMIT_MAX_WEIGHT_FREIGHT = 30;
	const LIMIT_MIN_WIDTH_FREIGHT = 11;
	const LIMIT_MAX_WIDTH_FREIGHT = 105;
	const LIMIT_MIN_HEIGHT_FREIGHT = 2;
	const LIMIT_MAX_HEIGHT_FREIGHT = 105;
	const LIMIT_MIN_LENGTH_FREIGHT = 16;
	const LIMIT_MAX_LENGTH_FREIGHT = 105;

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

	public static function destroySession()
	{
		unset($_SESSION[Cart::SESSION]);
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

	public function updateZipcode()
	{
		$sql = new Sql();
		$sql->query("UPDATE tb_carts SET deszipcode = :deszipcode WHERE idcart = :idcart", array(
			":deszipcode"=> $this->getdeszipcode(),
			":idcart"=> $this->getidcart()
		));
	}

	public function addProduct(Product $product)
	{
		$sql = new Sql();
		$sql->query("INSERT INTO tb_cartsproducts(idcart, idproduct) VALUES(:idcart, :idproduct)", array(
			":idcart"=> $this->getidcart(),
			":idproduct"=> $product->getidproduct()
		));

		$this->getCalculateTotal();
	}

	public function removeProduct(Product $product, $all = false)
	{
		$sql = new Sql();
		if($all)
		{
			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL", array(
				":idcart"=> $this->getidcart(),
				":idproduct"=> $product->getidproduct()
			));
		}
		else
		{
			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL LIMIT 1", array(
				":idcart"=> $this->getidcart(),
				":idproduct"=> $product->getidproduct()
			));
		}

		$this->getCalculateTotal();
	}

	public function getProducts()
	{
		$sql = new Sql();
		$results = $sql->select("SELECT b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl, COUNT(*) AS nrtotal, SUM(b.vlprice) AS vltotal
			FROM tb_cartsproducts a 
			INNER JOIN tb_products b USING(idproduct)
			WHERE a.idcart = :idcart AND a.dtremoved IS NULL
			GROUP BY b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl
			ORDER BY b.desproduct
		", [":idcart"=> $this->getidcart()]);

		return Product::checkList($results);
	}

	public function getProductsTotal()
	{
		$sql = new Sql();
		$results = $sql->select(
			"SELECT SUM(a.vlprice) AS vlprice, SUM(a.vlwidth) AS vlwidth, SUM(a.vlheight) AS vlheight, SUM(a.vllength) AS vllength, SUM(a.vlweight) AS vlweight, COUNT(*) AS nrtotal
			FROM tb_products a 
			INNER JOIN tb_cartsproducts b ON a.idproduct = b.idproduct
			WHERE b.idcart = :idcart AND b.dtremoved IS NULL
		", [":idcart"=> $this->getidcart()]);
		if((int)$results[0]["nrtotal"] > 0){
			return $results[0];
		}else{
			return [];
		}

	}

	public function setFreight($nrzipcode)
	{
		$nrzipcode = str_replace('-', '', $nrzipcode);
		$totals = $this->getProductsTotal();
		if(count($totals) > 0)
		{
			$totals = $this->checkValuesFreight($totals);
			$qs = http_build_query([
				"nCdEmpresa"=> '',
				"sDsSenha"=> '',
				"nCdServico"=> '04510',
				"sCepOrigem"=> '08226021',
				"sCepDestino"=> $nrzipcode,
				"nVlPeso"=> $totals['vlweight'],
				"nCdFormato"=> 1,
				"nVlComprimento"=> $totals['vllength'],
				"nVlAltura"=> $totals['vlheight'],
				"nVlLargura"=> $totals['vlwidth'],
				"nVlDiametro"=> '0',
				"sCdMaoPropria"=> 'S',
				"nVlValorDeclarado"=> $totals['vlprice'],
				"sCdAvisoRecebimento"=> 'S'
			]);
			
		
			$xml = simplexml_load_file('http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?'.$qs);

			

			$result = $xml->Servicos->cServico;

			if($result->MsgErro != ''){
				Cart::setMsgError($result->MsgErro);
			}else{
				Cart::clearMsgError();
			} 

			$prazo = (array)$result->PrazoEntrega;
			$this->setnrdays($prazo[0]);
			$this->setvlfreight(Cart::formatValueToDecimal($result->Valor));
			$this->setdeszipcode($nrzipcode);
			$this->save();
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function setMsgError($msg)
	{
		$_SESSION[Cart::SESSION_ERROR] = $msg;
	}

	public static function getMsgError()
	{
		$msg = (isset($_SESSION[Cart::SESSION_ERROR])) ? $_SESSION[Cart::SESSION_ERROR] : '';
		Cart::clearMsgError();
		return $msg;
	}

	public static function clearMsgError()
	{
		$_SESSION[Cart::SESSION_ERROR] = NULL;
	}

	public static function formatValueToDecimal($value):float
	{
		$value = str_replace('.', '', $value);
		return str_replace(',', '.', $value);
	}

	public function checkValuesFreight($totals)
	{
		if($totals['vllength'] < Cart::LIMIT_MIN_LENGTH_FREIGHT)
		{
			$totals['vllength'] = Cart::LIMIT_MIN_LENGTH_FREIGHT;
		}else if($totals['vllength'] > Cart::LIMIT_MAX_LENGTH_FREIGHT)
		{
			throw new Exception("Valor do Comprimento extrapolou do limite.");			
		}

		if($totals['vlweight'] < Cart::LIMIT_MIN_WEIGHT_FREIGHT)
		{
			$totals['vlweight'] = Cart::LIMIT_MIN_WEIGHT_FREIGHT;
		}else if($totals['vlweight'] > Cart::LIMIT_MAX_WEIGHT_FREIGHT)
		{
			throw new Exception("Valor do Peso extrapolou do limite.");			
		}

		if($totals['vlheight'] < Cart::LIMIT_MIN_HEIGHT_FREIGHT)
		{
			$totals['vlheight'] = Cart::LIMIT_MIN_HEIGHT_FREIGHT;
		}else if($totals['vlheight'] > Cart::LIMIT_MAX_HEIGHT_FREIGHT)
		{
			throw new Exception("Valor da Altura extrapolou do limite.");			
		}

		if($totals['vlwidth'] < Cart::LIMIT_MIN_WIDTH_FREIGHT)
		{
			$totals['vlwidth'] = Cart::LIMIT_MIN_WIDTH_FREIGHT;
		}else if($totals['vlwidth'] > Cart::LIMIT_MAX_WIDTH_FREIGHT)
		{
			throw new Exception("Valor da Largura extrapolou do limite.");			
		}

		return $totals;

	}

	public function updateFreight()
	{
		if($this->getdeszipcode() != '')
		{
			return $this->setFreight($this->getdeszipcode());
		}
	}
	public function getValues()
	{
		$this->getCalculateTotal();
		return parent::getValues();
	}

	public function getCalculateTotal()
	{
		if($this->updateFreight())
		{
			$totals = $this->getProductsTotal();
			$this->setvlsubtotal($totals['vlprice']);
			$this->setvltotal($totals['vlprice'] + $this->getvlfreight());
		}
		else
		{
			$this->setvlsubtotal(0);
			$this->setvltotal(0);
			$this->setvlfreight(0);
		}
		
	}

}
?>