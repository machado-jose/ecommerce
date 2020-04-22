<?php 
	
use \Ecommerce\Model\User;
use \Ecommerce\Model\Cart;

function formatPrice(float $price)
{
	return number_format($price, 2, ",", ".");
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