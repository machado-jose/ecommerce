<?php 

use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;

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

?>