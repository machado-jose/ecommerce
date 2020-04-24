<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\User;

$app->get('/forgot', function() {
    
	$page = new Page();
	$page->setTpl("forgot");
});

$app->post('/forgot', function() {
    
	$user = new User();
	$user->getForgot($_POST['email'], false);
	header("Location: /forgot/sent");
	exit;
});

$app->get('/forgot/sent', function(){
	$page = new Page();
	$page->setTpl("forgot-sent");
});

$app->get("/forgot/reset/:code", function($code){

	$user = User::validForgotDescrypt($code);
	$page = new Page();
	$page->setTpl("forgot-reset", array(
		"name"=>$user['desperson'],
		"code"=>$code
	));
});

$app->post("/forgot/reset", function(){

	$forgot = User::validForgotDescrypt($_POST['code']);
	User::setForgotUsed($forgot['idrecovery']);
	$user = new User();
	$user->get((int)$forgot['iduser']);
	$user->setPassword(User::cryptPassword($_POST["password"]));
	$page = new Page();
	$page->setTpl("forgot-reset-success");
});

?>