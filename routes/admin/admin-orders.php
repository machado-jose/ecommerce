<?php 

use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Order;
use \Ecommerce\Model\OrderStatus;
use \Ecommerce\Model\Cart;

$app->get('/admin/orders/:idorder/delete', function($idorder)
{

	User::verifyLogin();

	$order = new Order();
	$order->get((int)$idorder);
	$order->delete();

	header("Location: /admin/orders");
	exit;

});

$app->get('/admin/orders/:idorder/status', function($idorder){
	
	User::verifyLogin();

	$order = new Order();
	$order->get((int)$idorder);
	$page = new PageAdmin();
	$page->setTpl('order-status', [
		"order"=> $order->getValues(),
		"status"=> OrderStatus::listAll(),
		"msgError"=> Order::getMsgError(),
		"msgSuccess"=> Order::getMsgSuccess()
	]);
});

$app->post('/admin/orders/:idorder/status', function($idorder){
	
	User::verifyLogin();

	if(!isset($_POST['idstatus']) || !(int)$_POST['idstatus'] > 0)
	{
		Order::setMsgError("Informe o status do pedido.");
		header("Location: /admin/orders/".$idorder."/status");
		exit;
	}

	$order = new Order();
	$order->get((int)$idorder);
	
	$order->setidstatus((int)$_POST['idstatus']);
	$order->updateStatus();
	Order::setMsgSuccess("Status do pedido alterado com sucesso.");
	header("Location: /admin/orders/".$idorder."/status");
	exit;
});

$app->get('/admin/orders/:idorder', function($idorder)
{
	User::verifyLogin();

	$order = new Order();
	$order->get((int)$idorder);
	$cart = $order->getCart();

	$page = new PageAdmin();
	$page->setTpl('order', [
		"order"=> $order->getValues(),
		"cart"=> $cart->getValues(),
		"products"=> $cart->getProducts()
	]);
});

$app->get('/admin/orders', function()
{

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : '';
    $nrpage = (isset($_GET['page'])) ? $_GET['page'] : 1;

    $pagination = ($search != '') ? Order::getOrdersPageSearch($search, $nrpage) : Order::getOrdersPage($nrpage); 

    $pages = [];

    for($x = 1; $x <= $pagination['pages']; $x++)
    {
        array_push($pages, [
            'href'=> '/admin/orders?'.http_build_query([
                "page"=>$x,
                "search"=>$search
            ]),
            'text'=> $x
        ]);

    }

	$page = new PageAdmin();
	$page->setTpl('orders', [
		"orders"=> $pagination['data'],
        "search"=> $search,
        "pages"=> $pages
	]);

});

?>