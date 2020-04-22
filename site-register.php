<?php

use \Ecommerce\Page;
use \Ecommerce\Model\User;

$app->post('/register', function(){

	$_SESSION['registerValues'] = $_POST;

	if(!isset($_POST['name']) || $_POST['name'] === '')
	{
		User::setMsgError("O Nome Completo é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /login");
		exit;
	}
	if(!isset($_POST['email']) || $_POST['email'] === '')
	{
		User::setMsgError("O Email é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /login");
		exit;
	}
	if(!isset($_POST['deslogin']) || $_POST['deslogin'] === '')
	{
		User::setMsgError("O Login é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /login");
		exit;
	}
	if(!isset($_POST['phone']) || $_POST['phone'] === '')
	{
		User::setMsgError("O telefone é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /login");
		exit;
	}
	if(!isset($_POST['password']) || $_POST['password'] === '')
	{
		User::setMsgError("A senha é obrigatória.", User::SESSION_REGISTER_ERROR);
		header("Location: /login");
		exit;
	}

	if(User::checkLoginExists($_POST['deslogin']))
	{
		User::setMsgError("O Login informado já existe.", User::SESSION_REGISTER_ERROR);
		header("Location: /login");
		exit;
	}

	if(User::checkEmailExists($_POST['email']))
	{
		User::setMsgError("O Email informado já existe.", User::SESSION_REGISTER_ERROR);
		header("Location: /login");
		exit;
	}

	$user = new User();
	$user->setDatas(array(
		"desperson"=> $_POST['name'],
		"deslogin"=> $_POST['deslogin'],
		"despassword"=> $_POST['password'],
		"desemail"=> $_POST['email'],
		"nrphone"=> $_POST['phone'],
		"inadmin"=> 0
	));
	$user->save();
	User::loginToEmail($_POST['email'], $_POST['password']);
	header("location: /checkout");
	exit;
});

?>