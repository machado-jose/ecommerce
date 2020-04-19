<?php 

if((bool)!session_id()) session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once("utils.php");
require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-forgot.php");
require_once("admin-categories.php");
require_once("admin-products.php");

$app->get("/info", function(){
phpinfo();
});

$app->run();

?>