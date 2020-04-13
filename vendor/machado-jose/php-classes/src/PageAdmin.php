<?php 

namespace Ecommerce;

class PageAdmin extends Page
{
	public function __construct($opts = array(), $tpl_dir = "views" . DIRECTORY_SEPARATOR . "admin/"){
		parent::__construct($opts, $tpl_dir);
	}
}

?>