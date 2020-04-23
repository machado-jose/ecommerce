<?php 

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model\Model;
use \Ecommerce\Model\Product;

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
		Category::updateFile();
	}

	public function getProducts($related = true)
	{
		$sql = new Sql();

		if($related === true)
		{
			return $sql->select("SELECT * 
				FROM tb_products
				WHERE idproduct IN (
					SELECT a.idproduct
				    FROM tb_productscategories a
				    INNER JOIN tb_categories b
				    ON(a.idcategory = b.idcategory)
					WHERE b.idcategory = :idcategory
				)",[
				":idcategory"=> $this->getidcategory()
			]);
		}else
		{
			return $sql->select("SELECT * 
				FROM tb_products
				WHERE idproduct NOT IN (
					SELECT a.idproduct
				    FROM tb_productscategories a
				    INNER JOIN tb_categories b
				    ON(a.idcategory = b.idcategory)
					WHERE b.idcategory = :idcategory
				)",[
				":idcategory"=> $this->getidcategory()
			]);
		}
	}

	public function getProductsPage($page = 1, $itemsPerPage = 8)
	{
		$sql = new Sql();
		$start = ($page - 1) * $itemsPerPage;

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products a
			INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_categories c ON c.idcategory = b.idcategory
			WHERE c.idcategory = :idcategory
			LIMIT $start, $itemsPerPage
			", array(
				':idcategory'=> $this->getidcategory()
		));

		$resultsTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal");
		return [
			'data'=> Product::checkList($results),
			'total'=> (int)$resultsTotal[0]['nrtotal'],
			'pages'=> (int)ceil($resultsTotal[0]['nrtotal'] / $itemsPerPage)
		]; 
	}

	public function addProduct(Product $product)
	{
		$sql = new Sql();
		$sql->query("INSERT INTO tb_productscategories(idcategory, idproduct) VALUES(:idcategory, :idproduct)", array(
			":idcategory"=> $this->getidcategory(),
			":idproduct"=> $product->getidproduct()
		));
	}

	public function removeProduct(Product $product)
	{
		$sql = new Sql();
		$sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct", array(
			":idcategory"=> $this->getidcategory(),
			":idproduct"=> $product->getidproduct()
		));
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
		Category::updateFile();
	}

	private static function updateFile()
	{
		$category = Category::listAll();
		$html = [];
		foreach ($category as $row) {
			array_push($html, '<li><a href="/categories/'.$row['idcategory']. '/1">'.$row['descategory'].'</a></li>');		
		}
		$filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'categories-menu.html';
		file_put_contents($filename, implode('', $html));
	}

	public static function getCategoriesPage($page = 1, $itemsPerPage = 8)
	{
		$sql = new Sql();
		$start = ($page - 1) * $itemsPerPage;

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_categories 
			ORDER BY descategory
			LIMIT $start, $itemsPerPage");

		$resultsTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal");
		return [
			'data'=> $results,
			'total'=> (int)$resultsTotal[0]['nrtotal'],
			'pages'=> (int)ceil($resultsTotal[0]['nrtotal'] / $itemsPerPage)
		]; 
	}

	public static function getCategoriesPageSearch($search, $page = 1, $itemsPerPage = 8)
	{
		$sql = new Sql();
		$start = ($page - 1) * $itemsPerPage;

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_categories
			WHERE descategory LIKE :search
			ORDER BY descategory
			LIMIT $start, $itemsPerPage", [
				":search"=> '%'.$search.'%'
			]);

		$resultsTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal");
		return [
			'data'=> $results,
			'total'=> (int)$resultsTotal[0]['nrtotal'],
			'pages'=> (int)ceil($resultsTotal[0]['nrtotal'] / $itemsPerPage)
		]; 
	}

}
?>