<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;

class Address extends Model{
	
	public static function getCep($nrcep)
	{
		$nrcep = str_replace('-', '', $nrcep);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/$nrcep/json/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$datas = json_decode(curl_exec($ch), true);
		curl_close($ch);

		return $datas;
	}

	public static function validAddressDatas($datas)
	{

		if(isset($datas["bairro"]) && isset($datas["logradouro"]) && $datas["bairro"] != '')
		{
			if($datas["logradouro"] == '')
			{
				$datas["logradouro"] = $datas["bairro"];
			}
			return $datas;
		}
		else
		{
			return NULL;
		}
	}

	public function loadFromCep($zipcode)
	{

		$datas = Address::getCep($zipcode);
		$datas = Address::validAddressDatas($datas);

		if(isset($datas))
		{
			$this->setdesaddress($datas["logradouro"]);
			$this->setdescomplement($datas["complemento"]);
			$this->setdesdistrict($datas["bairro"]);
			$this->setdescity($datas["localidade"]);
			$this->setdesstate($datas["uf"]);
			$this->setdescountry("Brasil");
			$this->setdeszipcode($zipcode);
		}
		
	}

	public function save()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_addresses_save(
			:idaddress,
			:idperson,
			:desaddress,
			:descomplement,
			:descity,
			:desstate,
			:descountry,
			:deszipcode,
			:desdistrict
		)", array(
			":idaddress"=> $this->getidaddress(),
			":idperson"=> $this->getidperson(),
			":desaddress"=> $this->getdesaddress(),
			":descomplement"=> $this->getdescomplement(),
			":descity"=> $this->getdescity(),
			":desstate"=> $this->getdesstate(),
			":descountry"=> $this->getdescountry(),
			":deszipcode"=> $this->getdeszipcode(),
			":desdistrict"=> $this->getdesdistrict()
		));

		if(count($results) > 0)
		{
			$this->setDatas($results[0]);
		}
	}

	public function getFromPerson($idperson)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_addresses WHERE idperson = :idperson LIMIT 1", [":idperson"=> $idperson]);

		if(count($results) > 0)
		{
			$this->setDatas($results[0]);
		}
	}

	public function verifyAddressExists()
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_addresses WHERE idperson = :idperson AND deszipcode = :deszipcode", [
			":idperson"=> $this->getidperson(),
			":deszipcode"=> $this->getdeszipcode()
		]);
		
		if(count($results) > 0)
		{
			$this->setDatas($results[0]);
			return true;
		}
		else
		{
			return false;
		}
	}

}

?>