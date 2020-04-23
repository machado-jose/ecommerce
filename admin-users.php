<?php 

use \Ecommerce\PageAdmin;
use \Ecommerce\Model\User;

$app->get('/admin/users', function() {
    
    User::verifyLogin();
    $users = User::listAll();

    $search = (isset($_GET['search'])) ? $_GET['search'] : '';
    $nrpage = (isset($_GET['page'])) ? $_GET['page'] : 1;

    $pagination = ($search != '') ? User::getUsersPageSearch($search, $nrpage) : User::getUsersPage($nrpage); 

    $pages = [];
    for($x = 1; $x <= $pagination['pages']; $x++)
    {
        array_push($pages, [
            'href'=> '/admin/users?'.http_build_query([
                "page"=>$x,
                "search"=>$search
            ]),
            'text'=> $x
        ]);

    }

    $page = new PageAdmin();
    $page->setTpl("users", [
        "users"=> $pagination['data'],
        "search"=> $search,
        "pages"=> $pages
    ]);
    
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

?>