<?php 

namespace Ecommerce;
use Rain\Tpl;

class Page
{

	private $tpl;
	private $options = [];
	private $defaults = [
		"datas"=> []
	];

	public function __construct($opts = array())
	{

		$this->options = array_merge($this->defaults, $opts);

		$config = array(

			"tpl_dir"       => $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views/",
			"cache_dir"     => $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views-cache/",
			"debug"         => false
		);

		Tpl::configure( $config );
		$this->tpl = new Tpl();
		$this->setDatas($this->options);
		$this->tpl->draw("header");
	}

	public function setTpl($name, $datas = array(), $returnHtml = false)
	{
		$this->setDatas($datas);
		return $this->tpl->draw($name, $returnHtml);
	}
	
	private function setDatas($datas = array())
	{
		foreach ($datas as $key => $value) {
			$this->tpl->assign($key, $value);
		}
	}

	public function __destruct()
	{
		$this->tpl->draw( "footer" );
	}
}

?>