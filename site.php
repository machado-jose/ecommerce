<?php 

use \Ecommerce\Page;
use \Ecommerce\Model\Product;
use \Ecommerce\Model\Category;
use \Ecommerce\Model\Cart;
use \Ecommerce\Model\User;
use \Ecommerce\Model\Address;

$app->get('/', function() {
    $products = Product::listAll();
	$page = new Page();
	$page->setTpl("index", ["products"=>Product::checklist($products)]);
});

$app->get('/categories/:idcategory/:npage', function($idcategory, $npage){
	$category = new Category();
	$category->get((int)$idcategory);
	$pagination = $category->getProductsPage($npage);
	$pages = [];
	for($i = 1; $i <= $pagination['pages']; $i++)
	{
		array_push($pages, [
			"link"=> '/categories/'.$category->getidcategory().'/'.$i,
			"page"=> $i
		]);
	}
	$page = new Page();
	$page->setTpl("category", array(
		"category"=>$category->getValues(),
		"products"=>$pagination['data'],
		"pages"=>$pages
	));
});

$app->get('/products/:desurl', function($desurl){

	$product = new Product();
	$product->getFromUrl($desurl);
	$page = new Page();
	$page->setTpl("product-detail", array(
		"product"=>$product->getValues(),
		"categories"=>$product->getCategories()
	));
});

$app->get('/cart', function(){
	$cart = Cart::getFromSession();
	$page = new Page();	
	$page->setTpl('cart', array(
		"cart"=> $cart->getValues(),
		"products"=> $cart->getProducts(),
		"error"=> Cart::getMsgError(User::SESSION_ERROR)
	));
});

$app->get('/cart/:idproduct/add', function($idproduct){
	$product = new Product();
	$product->get($idproduct);
	$cart = Cart::getFromSession();
	$cart->addProduct($product);
	header("Location: /cart");
	exit;
});

$app->post('/cart/:idproduct/add', function($idproduct){
	$product = new Product();
	$product->get($idproduct);
	$cart = Cart::getFromSession();
	$qtd = (isset($_POST['qtd'])) ? $_POST['qtd'] : 1;
	for($i = 0; $i < $qtd; $i++)
	{
		$cart->addProduct($product);
	}
	header("Location: /cart");
	exit;
});

$app->get('/cart/:idproduct/minus', function($idproduct){
	$product = new Product();
	$product->get($idproduct);
	$cart = Cart::getFromSession();
	$cart->removeProduct($product);
	header("Location: /cart");
	exit;
});

$app->get('/cart/:idproduct/remove', function($idproduct){
	$product = new Product();
	$product->get($idproduct);
	$cart = Cart::getFromSession();
	$cart->removeProduct($product, true);
	header("Location: /cart");
	exit;
});

$app->post('/cart/freight', function(){
	$cart = Cart::getFromSession();
	$cart->setFreight($_POST['zipcode']);
	header("Location: /cart");
	exit;
});

$app->get('/checkout', function(){
	User::verifyLogin(false);
	$address = new Address();
	$cart = Cart::getFromSession();
	$page = new Page();
	$page->setTpl('checkout', array(
		"cart"=> $cart->getValues(),
		"address"=> $address->getValues()
	));
});

$app->get('/login', function(){
	$page = new Page();
	$page->setTpl('login', [
		"error"=> User::getMsgError(User::SESSION_ERROR),
		"errorRegister"=> User::getMsgError(User::SESSION_REGISTER_ERROR),
		"registerValues"=> isset($_SESSION['registerValues']) ? $_SESSION['registerValues'] : ["name"=> '', "email"=> '', "phone"=> '', "deslogin"=> '']
	]);
});

$app->post('/login', function(){
	try
	{
		User::loginToEmail($_POST['login'], $_POST['password']);
	}
	catch(\Exception $e)
	{
		User::setMsgError($e->getMessage(), User::SESSION_ERROR);
	}
	header("location: /checkout");
	exit;
});

$app->get('/logout', function(){
	User::logout();
	header("Location: /login");
	exit;
});

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

$app->get('/profile', function(){
	User::verifyLogin(false);
	$user = User::getFromSession();
	$page = new Page();
	$page->setTpl("profile", array(
		"user"=> $user->getValues(),
		"profileMsg"=> User::getMsgSuccess(),
		"profileError"=> User::getMsgError(User::SESSION_REGISTER_ERROR)
	));
});

$app->post('/profile', function(){

	User::verifyLogin(false);

	if(!isset($_POST['desperson']) || $_POST['desperson'] === '')
	{
		User::setMsgError("O Nome Completo é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /profile");
		exit;
	}
	if(!isset($_POST['desemail']) || $_POST['desemail'] === '')
	{
		User::setMsgError("O Email é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /profile");
		exit;
	}
	if(!isset($_POST['deslogin']) || $_POST['deslogin'] === '')
	{
		User::setMsgError("O Login é obrigatório.", User::SESSION_REGISTER_ERROR);
		header("Location: /profile");
		exit;
	}

	$user = User::getFromSession();

	if($_POST['deslogin'] !== $user->getdeslogin())
	{
		if(User::checkLoginExists($_POST['deslogin']))
		{
			User::setMsgError("O Login informado já existe.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile");
			exit;
		}
	}

	if($_POST['desemail'] !== $user->getdesemail())
	{
		if(User::checkEmailExists($_POST['desemail']))
		{
			User::setMsgError("O Email informado já existe.", User::SESSION_REGISTER_ERROR);
			header("Location: /profile");
			exit;
		}
	}
	
	$_POST['inadmin'] = $user->getinadmin();
	$_POST['despassword'] = $user->getdespassword();

	$user->setDatas($_POST);
	$user->update();
	$user->updateSession();

	User::setMsgSuccess("Dados Alterados com sucesso.");

	header("Location: /profile");
	exit;
})

?>