<?php
class API_Parent extends CI_Controller {
	//used to make sure the construct of the parent always gets executed
	public $sessionData;
	public $userId;
	public function __construct($checkLogin=true,$newMethod=false) {
		parent::__construct();
		$this->sessionData=$this->session->get_userdata();
		if(!isset($this->sessionData['noForge'])){
			$this->load->helper("string");
			$this->sessionData['noForge']=random_string("alnum",8);
			$this->session->set_userdata(array("noForge"=>$this->sessionData['noForge']));
		}
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
		
		if($newMethod && $checkLogin){
			$this->forceAuthorized();
		} elseif ($checkLogin){
			$this->userId=$this->getIdForced();
		}
	}
	//check if the request really comes from the user and not something else
	public function checkLegit($code,$mode="error",$to="profile"){
		if($code != $this->sessionData['noForge']){
			if($mode=="redirect") {
				redirect($to);
			}elseif($mode=="die"|| $mode=="exit"){
				exit;
			} else {
				return array("success"=>false,"error"=>"Strings don't match'");
			}
		}
		return array("success"=>true);
	}
	private $putValues;
	public $usedJSON = null;
	public function getPut(){
		if(empty($this->putValues)){
			if(!$this->input->raw_input_stream){
				return [];
			}
			//TODO make sure to check if parse_str didn't get too much stuff to work with.
			//php is "nice" enough to just continue with truncated data if it does.
			$this->usedJSON = false;
			parse_str($this->input->raw_input_stream,$this->putValues);
			//var_dump($this->putValues) ;
			if(count($this->putValues)<=1){
				$data = (array)json_decode($this->input->raw_input_stream,true);
				if($data){
					$this->putValues = $data;
					//echo "lol?";
					$this->usedJSON = true;
				}
			}
		}
		return $this->putValues;
	}
	protected function block(){
		$this->output->set_status_header(403)->_display();
		die();
	}
	public function checkIsLoggedIn(){
		return $this->session->has_userdata("userId");
	}
	//used to force a login
	public function forceLogIn(){
		if(!$this->checkIsLoggedIn()){
			$this->block();
		}
	}
	//used to force a log in and get the user id as well
	public function getIdForced(){
		$this->forceLogIn();
		return $this->session->userId;
	}
	public function redirectLoggedIn(){
		if($this->session->has_userdata("userId")){
			redirect("api/users/".$this->getIdForced());
			echo json_encode(["userId"=>$this->session->userId]);
			die();
		}
	}

	//this function just uses $this->form_validation->run() to see if all the data is present and give the correct responce back to the client if it doesn't
	//if $data= false then it uses post data, just as $this->form_validation->run() would.
	//depending on $XSSClean it cleans the data before returning it
	public function checkAndErr($checkOn,$data=false){
		$this->load->library('form_validation');
		$this->form_validation->reset_validation();
		if($data===false){
			$data= $this->input->post();
			if(! $data){
				$data = $this->getPut();
			}
		}
		$this->form_validation->set_data($data);
		foreach($checkOn as $key=>$value){
			$this->form_validation->set_rules($value[0],$value[1],$value[2]);
		}
		//$this->form_validation->set_rules($checkOn);
		if($this->form_validation->run()){
			if($data===false){
				$data = $this->input->post();
			}
			return $data;
		} else {
			//seemed the request was missing some things :(
			//Guess we need to generate an error.
			$this->load->view("missingFields");
		}
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
	}
	private function checkIsPad(){
		$this->config->load("socket");
		$header = $this->input->get_request_header("pad_token");
		return $header === $this->config->item("pad_token");
	}
	public function forcePadServer(){
		if(!$this->checkIsPad()){
			$this->output->set_status_header(422);
			$this->outputPlusFilter(["message"=>"token missmatch"])->_display();
			die();
		} else {
			return true;
		}
	}
	public function forceAuthorized(){
		$isAuthorized = $this->checkIsLoggedIn();
		if($isAuthorized){
			$this->userId = $this->getIdForced();
		} else {
			$isAuthorized = $this->checkIsPad();
		}
		if(!$isAuthorized ){
			parent::block();
		}
	}
}
