<?php
	require_once("Rest.inc.php");
	class Admin{
        var $id;
        var $name;
        var $email;
    }
	class API extends REST {
		public $data = "";
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "";
		const DB = "csv";
		private $db = NULL;
		public function __construct(){
			parent::__construct();
			$this->dbConnect();
		}
		/*
		 *  Database connection 
		*/
		private function dbConnect(){
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
				mysql_select_db(self::DB,$this->db);
		}
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
			$func =	'';
			if(isset($_REQUEST['rquest'])){
				$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			}
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);// If the method not exist with in this class, response would be "Page not found".
		}
		/* 
		 *	gettime API
		 *  gettime cant GET method
		 */
		private function getTime(){
			// If success everythig is good send header as "OK" and user details
			$result	=	array('timeServer',date('Y-m-d H:i:s',time()));
			$this->response($this->json($result), 200);
			//$this->response('', 204);	// If no records "No Content" status
			//If invalid inputs "Bad Request" status message and reason
			//$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			//$this->response($this->json($error), 400);
		}

		/* 
		 *	users API
		 *  users cant GET method
		 */
		private function users() 
		{ 
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			if($this->get_request_method() != "GET") { 
				$this->response('',406); 
			} 
			$sql = mysql_query("SELECT * FROM tbl_admin", $this->db); 
			if(mysql_num_rows($sql) > 0) { 
				$result = array(); 
				while($seletedItem = mysql_fetch_array($sql)) { 
					$item = new Admin();
					$item->id = $seletedItem['id'];
					$item->name = $seletedItem['name'];
					$item->email = $seletedItem['email'];
					$result[] = $item;
				} 
				// If success everythig is good send header as "OK" and return list of users in JSON format 
				$this->response($this->json($result), 200); 
			} 
			$this->response('',204); // If no records "No Content" status 
		}

		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	// Initiiate Library
	$api = new API;
	$api->processApi();
?>