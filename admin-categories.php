<?php 

use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Category;

$app->get('/admin/categories', function(){
	User::verifyLogin();
	$categories = Category::listAll();
	$page = new PageAdmin();
	$page->setTpl("categories",["categories"=>$categories]);
});

$app->get('/admin/categories/create', function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("categories-create");
});

$app->post('/admin/categories/create', function(){
	User::verifyLogin();
	$category = new Category();
	$category->setDatas($_POST);
	$category->save();
	header('Location: /admin/categories');
	exit;
});

$app->get('/admin/categories/:idcategor/delete', function($idcategory){
	User::verifyLogin();
	$category = new Category();
    $category->get((int)$idcategory);
    $category->delete();
    header('Location: /admin/categories');
	exit;
});

$app->get('/admin/categories/:idcategory', function($idcategory){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new PageAdmin();
	$page->setTpl("categories-update", $category->getValues());
});

$app->post('/admin/categories/:idcategory', function($idcategory){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$category->setDatas($_POST);
	$category->save();
	header('Location: /admin/categories');
	exit;
});

$app->get('/categories/:idcategory', function($idcategory){
	$category = new Category();
	$category->get($idcategory);
	/*var_dump($category->getValues());
	exit;*/
	$page = new Page();
	$page->setTpl("category", array(
		"category"=>$category->getValues(),
		"products"=>[]
	));
});

?>