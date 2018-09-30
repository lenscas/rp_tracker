<?php
spl_autoload_register(function ($class_name) {
	$load = function($str){
		$possible = $str . ".php";
		if(is_file($possible)){
			include $possible;
			return true;
		}
	};
	$load(APPPATH . "/classes/" . $class_name) ||
	$load(APPPATH . "/classes/errors/" . $class_name) ||
	$load(APPPATH . "/services/" . $class_name);

});
class API_Parent extends CI_Controller {
	public $sessionData;
	public $userId;
	public function __construct(string $model, bool $checkLogin=true) {
		parent::__construct();
		$this->output->set_content_type('application/json');
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}
		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				// may also be using PUT, PATCH, HEAD etc
				header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: ". $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
			exit(0);
		}
		$this->user = new User();
		if($checkLogin){
			$this->user->forceAuthorized();
		}
		$this->body = new Input();
		$this->setOutput = new Output();
		$this->load->model($model);
	}


	//this function is used if a resource is made to uniformaly return stuff to the client
	public function niceMade($data,$urlPart="",$resourceKind="",$resourceName="",$pref=3,$correctReturn=201){
		$id = null;
		$viewData = array();
		if(gettype($data)=="array"){
			$viewData["urlPart"]       = $data["url"]          ?? "";
			$viewData["resourceKind"]  = $data["resourceKind"] ?? "";
			$viewData["resourceName"]  = $data["resourceName"] ?? "";
			$viewData["pref"]          = $data["pref"]         ?? 3;
			$viewData["correctReturn"] = $data["code"]         ?? 201;
			$viewData["id"]            = $data["id"]           ?? null;
			$viewData["errored"]       = $data["status"]       ?? RP_ERROR_NONE;
			$viewData["customError"]   = $data["custError"]    ?? null;
			$viewData["data"]          = $data["data"]         ?? null;
			if($data["alertData"] ?? false){
				try{
					$this->load->view("alert",["alertData"=>$data["alertData"]]);
				}
				catch(Exception $e){
					error_log("couldn't register.");
				}
			}
		} else {
			$viewData["errored"]       = $data;
			$viewData["urlPart"]       = $urlPart;
			$viewData["resourceKind"]  = $resourceKind;
			$viewData["resourceName"]  = $resourceName;
			$viewData["pref"]          = $pref;
			$viewData["correctReturn"] = $correctReturn;
			$viewData["id"]            = $id;
		}
		$viewData["newItemCreated"]    = true;
		$this->load->view("basicOutput",$viewData);
	}
	public function niceReturn($data,$responce=200){
		$viewData = array();
		if(gettype($responce)=="array"){
			$viewData["correctReturn"] = $responce["code"] ?? 200;
			if($responce["alertData"] ?? false){
				$this->load->view("alert",["alertData"=>$responce["alertData"]]);
			}
		} else {
			$viewData["correctReturn"] = $responce;
		}
		$data = (array)$data;
		if(! ($data["data"] ?? false)){
			$data = ["data"=>$data];
		}
		$viewData["data"] = $data["data"];
		$viewData["errored"] = RP_ERROR_NONE;
		if(empty($data["data"])){
			$viewData["errored"] = RP_ERROR_NOT_FOUND;
		}
		$this->load->view("basicOutput",$viewData);
		die();
	}

}
