<?php 

namespace Ecommerce\Model;

class Model{

	// Os valores de cada campo será armazenado n array $values
	private $values = [];

	// O objetivo é chamar os métodos Get e Set dinamicamente
	// A função __call vai ser chamada caso não existe o método
	public function __call($name, $args){

		$method = substr($name, 0, 3);
		$fieldName = substr($name, 3, strlen($name));

		switch($method){

			case 'get':
				return $this->values[$fieldName];
				break;
			case 'set':
				$this->values[$fieldName] = $args[0];
				break;
		}
	}

	public function setDatas($datas = array()){
		foreach ($datas as $key => $value) {
			$this->{"set".$key}($value);
		}
	}

	public function getValues(){
		return $this->values;
	}
}

?>