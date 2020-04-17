<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;

class Product extends Model
{

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
	}

	public static function checkList($list)
	{
		foreach ($list as &$row) {
			$p = new Product();
			$p->setDatas($row);
			$row = $p->getValues();
		}

		return $list;
	}

	public function save()
	{
		$sql = new Sql();

		// Obs.: Observar o número de bytes que as variáveis da procedure são capazes de armazenar
		$results = $sql->select("CALL sp_products_save(
			:pidproduct,
			:pdesproduct,
			:pvlprice,
			:pvlwidth,
			:pvlheight,
			:pvllength,
			:pvlweight,
			:pdesurl
		)", array(
			":pidproduct"=> $this->getidproduct(),
			":pdesproduct"=> $this->getdesproduct(),
			":pvlprice"=> $this->getvlprice(),
			":pvlwidth"=> $this->getvlwidth(),
			":pvlheight"=> $this->getvlheight(),
			":pvllength"=> $this->getvllength(),
			":pvlweight"=> $this->getvlweight(),
			":pdesurl"=> $this->getdesurl()
		));

		$this->setDatas($results[0]);
	}

	public function get($idproduct)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", array(":idproduct"=> $idproduct));
		$this->setDatas($results[0]);
	}

	public function delete()
	{
		$sql = new Sql();
		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", array(
			":idproduct"=> $this->getidproduct()
		));
	}

	private function checkPhoto()
	{
		$filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR.
			'res' . DIRECTORY_SEPARATOR .
			'site' . DIRECTORY_SEPARATOR .
			'img' . DIRECTORY_SEPARATOR .
			'products' . DIRECTORY_SEPARATOR .
			$this->getidproduct() . '.jpg';

		file_exists($filename) ? $url = '/res/site/img/products/' . $this->getidproduct() . '.jpg' : $url = '/res/site/img/boxed-bg.jpg';

		return $this->setdesphoto($url);
	}

	public function getValues()
	{
		$this->checkPhoto();
		return parent::getValues();
	}

	public function setPhoto($file)
	{
		$extension = explode('.', $file["name"]);
		$extension = end($extension);
		switch ($extension) {
			case 'jpg':
			case 'jpeg':
				$image = imagecreatefromjpeg($file["tmp_name"]);
				break;
			
			case 'gif':
				$image = imagecreatefromgif($file["tmp_name"]);
				break;
			case 'png':
				$image = imagecreatefrompng($file["tmp_name"]);
				break;
		}

		$filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR.
			'res' . DIRECTORY_SEPARATOR .
			'site' . DIRECTORY_SEPARATOR .
			'img' . DIRECTORY_SEPARATOR .
			'products' . DIRECTORY_SEPARATOR .
			$this->getidproduct() . '.jpg';

		imagejpeg($image, $filename);
		imagedestroy($image);
		$this->checkPhoto();
	}

}
?>