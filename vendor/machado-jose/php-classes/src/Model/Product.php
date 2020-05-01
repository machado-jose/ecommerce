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
		$sql->query("UPDATE tb_products SET salestatus = 0 WHERE idproduct = :idproduct", array(
			":idproduct"=> $this->getidproduct()
		));
		$this->deleteDescription();
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

	public function getFromUrl($desurl)
	{
		$sql = new Sql();
		$row = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1", [":desurl"=> $desurl]);
		$this->setDatas($row[0]);
	}

	public function getCategories()
	{
		$sql = new Sql();
		return $sql->select("SELECT * FROM tb_categories a INNER JOIN tb_productscategories b ON a.idcategory = b.idcategory WHERE b.idproduct = :idproduct", [":idproduct"=> $this->getidproduct()]);
	}

	public static function getProductsPage($page = 1, $itemsPerPage = 10)
	{
		$sql = new Sql();
		$start = ($page - 1) * $itemsPerPage;

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products
			WHERE salestatus = 1
			ORDER BY desproduct
			LIMIT $start, $itemsPerPage");

		$results = Product::checkList($results);
		
		$resultsTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal");
		return [
			'data'=> $results,
			'total'=> (int)$resultsTotal[0]['nrtotal'],
			'pages'=> (int)ceil($resultsTotal[0]['nrtotal'] / $itemsPerPage)
		]; 
	}

	public static function getProductsPageSearch($search, $page = 1, $itemsPerPage = 10)
	{
		$sql = new Sql();
		$start = ($page - 1) * $itemsPerPage;

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products
			WHERE desproduct LIKE :search AND salestatus = 1
			ORDER BY desproduct
			LIMIT $start, $itemsPerPage", [
				":search"=> '%'.$search.'%'
			]);

		$results = Product::checkList($results);

		$resultsTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal");
		return [
			'data'=> $results,
			'total'=> (int)$resultsTotal[0]['nrtotal'],
			'pages'=> (int)ceil($resultsTotal[0]['nrtotal'] / $itemsPerPage)
		]; 
	}

	public function getDescription()
	{

		$filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			'res' . DIRECTORY_SEPARATOR .
			'site' . DIRECTORY_SEPARATOR .
			'products-description' . DIRECTORY_SEPARATOR .
			$this->getidproduct() . '.html';

		$path = '..'.DIRECTORY_SEPARATOR.
		    '..'. DIRECTORY_SEPARATOR .
		    'res' . DIRECTORY_SEPARATOR .
			'site' . DIRECTORY_SEPARATOR .
			'products-description' . DIRECTORY_SEPARATOR .
			$this->getidproduct();

		if(file_exists($filename))
		{
			$file = fopen($filename, "r");
			$content = file_get_contents($filename);
			fclose($file);
			$this->setdescription($content);
			$this->setpath($path);
		}
		
	}

	public function saveDescription($content)
	{
	
		$filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			'res' . DIRECTORY_SEPARATOR .
			'site' . DIRECTORY_SEPARATOR .
			'products-description' . DIRECTORY_SEPARATOR .
			$this->getidproduct() . '.html';

		$file = fopen($filename, "w+");
		fwrite($file, $content);
		fclose($file);
	}

	public function deleteDescription()
	{
	
		$filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			'res' . DIRECTORY_SEPARATOR .
			'site' . DIRECTORY_SEPARATOR .
			'products-description' . DIRECTORY_SEPARATOR .
			$this->getidproduct() . '.html';

		if(file_exists($filename)) unlink($filename);
	}

}
?>