<?php 
	
use Rain\Tpl;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Cart;

function formatPrice($price = NULL)
{
	return ($price) ? number_format($price, 2, ",", ".") : 0;
}

function formatDate($date)
{
	return date('d/m/Y', strtotime($date));
}

function checkLogin($inadmin = true)
{
	return User::checkLogin($inadmin);
}

function getUserName()
{
	$user = User::getFromSession();
	return $user->getdeslogin();
}

function getCartNrTotal()
{
	$cart = Cart::getFromSession();
	$totals = $cart->getProductsTotal();
	return $totals["nrtotal"];
}

function getCartVlSubtotal()
{
	$cart = Cart::getFromSession();
	$totals = $cart->getProductsTotal();
	return formatPrice($totals["vlprice"]);
}

?>