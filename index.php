<?php 

if((bool)!session_id()) session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Ecommerce\Page;
use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Category;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();
	$page->setTpl("index");
});

$app->get('/admin', function() {
    
    User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("index");
});

$app->get('/admin/login', function() {
    
	$page = new PageAdmin(array(
		"header"=> false,
		"footer"=> false
	));
	$page->setTpl("login");
});

$app->get('/admin/forgot', function() {
    
	$page = new PageAdmin(array(
		"header"=> false,
		"footer"=> false
	));
	$page->setTpl("forgot");
});

$app->post('/admin/forgot', function() {
    
	$user = new User();
	$user->getForgot($_POST['email']);
	header("Location: /admin/forgot/sent");
	exit;
});

$app->get('/admin/forgot/sent', function(){
	$page = new PageAdmin(array(
		"header"=> false,
		"footer"=> false
	));
	$page->setTpl("forgot-sent");
});

$app->get("/admin/forgot/reset/:code", function($code){

	$user = User::validForgotDescrypt($code);
	$page = new PageAdmin(array(
		"header"=> false,
		"footer"=> false
	));
	$page->setTpl("forgot-reset", array(
		"name"=>$user['desperson'],
		"code"=>$code
	));
});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDescrypt($_POST['code']);
	User::setForgotUsed($forgot['idrecovery']);
	$user = new User();
	$user->get((int)$forgot['iduser']);
	$user->setPassword(User::cryptPassword($_POST["password"]));
	$page = new PageAdmin(array(
		"header"=> false,
		"footer"=> false
	));
	$page->setTpl("forgot-reset-success");
});

$app->post('/admin/login', function(){

	User::login($_POST['deslogin'], $_POST['despassword']);
	header("Location: /admin");
	exit;
});

$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});

$app->get('/admin/users', function() {
    
    User::verifyLogin();
    $users = User::listAll();
	$page = new PageAdmin();
	$page->setTpl("users", ["users"=> $users]);
	
});

$app->get('/admin/users/create', function() {
    
    User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("users-create");
	
});

$app->get('/admin/users/:iduser/delete', function($iduser) {
    
    User::verifyLogin();
    $user = new User();
    $user->get((int)$iduser);
    $user->delete();
    header("Location: /admin/users");
    exit;
	
});

$app->get('/admin/users/:iduser', function($iduser) {
    
    User::verifyLogin();
	$page = new PageAdmin();
	$user = new User();
	$user->get((int)$iduser);
	$page->setTpl("users-update", array(
		"user"=> $user->getValues()
	));
	
});

$app->post('/admin/users/create', function() {
    
    User::verifyLogin();
    $user = new User();
    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;
    $user->setDatas($_POST);
    $user->save();
    header('Location: /admin/users');
    exit;
	
});

$app->post('/admin/users/:iduser', function($iduser) {
    
    User::verifyLogin();
	$user = new User();
    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;
    $user->get($iduser);
    $user->setDatas($_POST);
    $user->update();
    //var_dump($user);
    header('Location: /admin/users');
    exit;
});

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

$app->run();

?>