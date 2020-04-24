<?php 

if((bool)!session_id()) session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once("utils.php");
require_once("routes/site/site.php");
require_once("routes/site/site-forgot.php");
require_once("routes/site/site-categories.php");
require_once("routes/site/site-cart.php");
require_once("routes/site/site-checkout.php");
require_once("routes/site/site-products.php");
require_once("routes/site/site-profile.php");
require_once("routes/site/site-register.php");
require_once("routes/site/site-order.php");
require_once("routes/admin/admin.php");
require_once("routes/admin/admin-users.php");
require_once("routes/admin/admin-forgot.php");
require_once("routes/admin/admin-categories.php");
require_once("routes/admin/admin-products.php");
require_once("routes/admin/admin-orders.php");

$app->run();

?>