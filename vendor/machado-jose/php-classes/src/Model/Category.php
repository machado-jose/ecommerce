<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;

class Category extends Model
{

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
	}

	public function save()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_categories_save(
			:pidcategory,
			:pdescategory
		)", array(
			":pidcategory"=> $this->getidcategory(),
			":pdescategory"=> $this->getdescategory()
		));

		$this->setDatas($results[0]);
	}

	public function get($idcategory)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(":idcategory"=> $idcategory));
		$this->setDatas($results[0]);
	}

	public function delete()
	{
		$sql = new Sql();
		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", array(
			":idcategory"=> $this->getidcategory()
		));
	}

}
?>