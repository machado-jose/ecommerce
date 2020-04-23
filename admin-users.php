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

$app->get('/admin/users/:iduser/password', function($iduser){

    User::verifyLogin();

    $user = new User();
    $user->get($iduser);

    $page = new PageAdmin();
    $page->setTpl("users-password", array(
        "user"=> $user->getValues(),
        "msgError"=> User::getMsgError(User::SESSION_REGISTER_ERROR),
        "msgSuccess"=> User::getMsgSuccess(User::SESSION_SUCCESS)
    ));

});

$app->post('/admin/users/:iduser/password', function($iduser){

    User::verifyLogin();

    $user = new User();
    $user->get($iduser);

    if(!isset($_POST['despassword']) || $_POST['despassword'] === '')
    {
        User::setMsgError("Digite a nova senha.", User::SESSION_REGISTER_ERROR);
            header("Location: /admin/users/".$iduser."/password");
            exit;
    }

    if(!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm'] === '')
    {
        User::setMsgError("Confirme a nova senha.", User::SESSION_REGISTER_ERROR);
            header("Location: /admin/users/".$iduser."/password");
            exit;
    }

    if($_POST['despassword'] !== $_POST['despassword-confirm'])
    {
        User::setMsgError("Confirme corretamente a senha.", User::SESSION_REGISTER_ERROR);
            header("Location: /admin/users/".$iduser."/password");;
            exit;
    }

    $user->setdespassword($_POST['despassword']);
    $user->update();
    User::setMsgSuccess("Senha alterada com sucesso.");
    header("Location: /admin/users/".$iduser."/password");
    exit;

});

?>