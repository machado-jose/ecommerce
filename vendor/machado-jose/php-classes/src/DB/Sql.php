<?php 

	namespace Ecommerce\DB;

	class Sql {

		const HOSTNAME = "127.0.0.1";
		const DBNAME = "db_ecommerce";
		const USERNAME = "root";
		const PASSWORD = "root";

		private $dbh;

		public function __construct(){

			$this->dbh = new \PDO("mysql:host=". Sql::HOSTNAME .";dbname=".Sql::DBNAME, Sql::USERNAME, Sql::PASSWORD);

		}

		private function setParams($statment,$parameters = array()){

			foreach ($parameters as $key => $value) {
				
				$this->setParam($statment,$key,$value);

			}

		}

		private function setParam($statment,$key,$value){

			$statment->bindParam($key,$value);

		}

		public function query($rawQuery,$params = array()){

			$stmt = $this->dbh->prepare($rawQuery);
			$this->setParams($stmt,$params);
			$stmt->execute();
			return $stmt;

		}

		public function select($rawQuery,$params = array()):array{

			$stmt = $this->query($rawQuery,$params);
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);

		}

	}

?>