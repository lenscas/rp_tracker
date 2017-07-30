<?php
class RP_Parent extends User_Parent {
	
	public function __construct() {
		parent::__construct();
		$this->userId=parent::getIdForced();
	}
}

class User_Parent extends CI_Controller {
	//used to make sure the construct of the parent always gets executed
	public $sessionData;
	public $userId;
	public function __construct() {
		parent::__construct();
		$this->sessionData=$this->session->get_userdata();
		if(!isset($this->sessionData['noForge'])){
			$this->load->helper("string");
			$this->sessionData['noForge']=random_string("alnum",8);
			$this->session->set_userdata(array("noForge"=>$this->sessionData['noForge']));
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
	//automatically cleans the input
	public function getPostSafe($alsoGiveError=false){
		//$this->load->library("security");
		if($alsoGiveError){
			$text=$this->input->post();
			$clean=$this->security->xss_clean($this->input->post());
			$safe=false;
			if($clean===$text){
				$safe=true;
			}
			return array("safe"=>$safe,"clean"=>$clean,"raw"=>$text);
		} else {
			return $this->security->xss_clean($this->input->post());
		}
	}
	private $putValues;
	public function getPutSafe($alsoGiveError=false){
		if($alsoGiveError){
			$put=$this->getPut();
			$cleanPut=$this->security->xss_clean($put);
			$safe=false;
			if($clean===$text){
				$safe=true;
			}
			return array("safe"=>$safe,"clean"=>$clean,"raw"=>$put);
		} else {
			return $this->security->xss_clean($this->getPut());
		}
	}
	public function getPut(){
		if(empty($this->putValues)){
			//we can only access the PUT values from php's input, for some reason
			//we also need to parse them first
			parse_str(file_get_contents("php://input"),$this->putValues);
		}
		return $this->putValues;
	}
	//used to force a login
	public function forceLogIn(){
		if(!$this->session->has_userdata("userId")){
			//if the call is done using ajax we want to be nice and tell the client that he isn't autorazied
			//if the call is done normally from the browser we just want to redirect
			if($this->input->is_ajax_request()){
				$this->output->set_status_header(403)->_display();
			} else {
				redirect("login");
			}
			die();
		}
	}
	//used to force a log in and get the user id as well
	public function getIdForced(){
		$this->forceLogIn();
		return $this->session->userId;
	}
	public function redirectLoggedIn(){
		if($this->session->has_userdata("userId")){
			redirect("profile");
		}
	}
	//only loads the header
	public function loadHeader($dataOverWrite=false){
		//$this->load->model("defaults_model.php");
		$headerData=array();
		if($dataOverWrite){
			$headerData=$dataOverWrite;
			
		} else {
			if($this->session->has_userdata("userId")){
				$headerData['loggedIn']=true;
			}else{
				$headerData['loggedIn']=false;
			}
		}
		$this->load->view("front/defaults/header.php",$headerData);
	}
	
	public function loadAll($view,$rpData,$data=array(),$overWriteHeader=false){
		$this->loadHeader($overWriteHeader);
		$this->load->view("front/defaults/firstSideBar");
		//we need to check if the user has joined this rp. Because depending on that we either show a create character button or join rp button
		//first, check if the rp model has already been loaded
		if(! isset($this->Rp_model)){
			$this->load->model("Rp_model");
		}
		$rpHeaderData=array();
		//echo gettype($dat)
		if(gettype($rpData)!="array"){
			$rpHeaderData=array(
				"rpCode"	=>	$rpData,
				"hasJoined"	=>	$this->Rp_model->checkIfJoined($this->userId,false,$rpData),
				"isGM"		=>	$this->Rp_model->checkIfGM($this->userId,$rpData)
			);
		} else {
			echo "test";
		}
		$this->load->view("front/defaults/rp_header",$rpHeaderData);
		$this->load->view("front/defaults/alert",["hasRPHeader"=>true]);
		$this->load->view("front/".$view,$data);
		$this->load->view("front/defaults/rp_header_end");
		$this->load->view("front/defaults/secondSideBar");
		$this->load->view("front/defaults/footer.php");
	}
	//loads the header, the sidebars, the specified view and the footer
	public function loadWithExtra($view,$data=array(),$overWriteHeader=false){
		$this->loadHeader($overWriteHeader);
		$this->load->view("front/defaults/firstSideBar");
		$this->load->view("front/defaults/alert",["hasRPHeader"=>false]);
		$this->load->view("front/".$view,$data);
		$this->load->view("front/defaults/secondSideBar");
		$this->load->view("front/defaults/footer.php");
	}
	//used to load the default header+a normal view+footer
	public function loadbasics($view,$data=array()){
		$this->loadHeader(false);
		$this->load->view("front/".$view,$data);
		$this->load->view("front/defaults/footer.php");
		
	}
}
class API_Parent extends User_Parent{
	public function __construct($checkLogin=true){
		parent::__construct();
		if($checkLogin){
			$this->userId=parent::getIdForced();
		}
		$this->output->set_content_type('application/json');
		//lets define some error codes.
		define("RP_ERROR_NONE",0);//NO ERRORS!
		define("RP_ERROR_DUPLICATE",1);//something that needs to be unique in the db wasn't
		define("RP_ERROR_NOT_FOUND",2);//someting that had to be updated couldn't be found
		define("RP_ERROR_NO_PERMISSION",3);//The user wanted to update something he had no permission for
		define("RP_ERROR_GENERIC",100);//a very generic error occured. :(

	}
	//this function just uses $this->form_validation->run() to see if all the data is present and give the correct responce back to the client if it doesn't
	//if $data= false then it uses post data, just as $this->form_validation->run() would.
	//depending on $XSSClean it cleans the data before returning it
	public function checkAndErr($checkOn,$data=false,$XSSClean=true,$pref=3){
		$this->load->library('form_validation');
		if($data===false){
			$data= $this->input->post();
			if(! $data){
				$fromInput = $this->input->input_stream();
				if(count($fromInput)==1){
					$data = (array)json_decode($this->input->raw_input_stream);
					if(!$data){
						$data=$fromInput;
					}
				} else {
					$data = $fromInput;
				}
				
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
			if($XSSClean){
				$data = html_escape($data);
			}
			return $data;
		} else {
			$this->form_validation->run();
			//seemed the request was missing some things :(
			//Guess we need to generate an error.
			$this->output->set_status_header(422);
			$body = [
				"messages" => [
					"One or more required fields where not filled in correctly.",
					"[ERROR]",
					"Sorry, but the following errors have been found.\n [ERRORS]"
				],
				"errors" => $this->form_validation->error_array()
			];
			$this->outputPlusFilter($body)->_display();
			//we don't want the program to do other stuff. So....lets die
			die();
		}
	}
	//this function is written to get arround the fact that html_escape does not work with multidimensional arrays and/or objects in objects
	//it solves this problem by using recursion.
	private function escapeData($data){
		//check if we are dealing with an array or an object
		$dataType=gettype($data);
		if($dataType=="array" || $dataType=="object"){
			//we are dealing with an object/array. To make it save to json_encode we need to escape all its values
			//start by looping over it
			foreach($data as $key=>$value){
				//get the escaped value
				$newData = $this->escapeData($value);
				//because php's syntax is diffrent for setting values in objects and arrays we need to check which of the two we are dealing with
				if($dataType=="object"){
					//could have written it as $data->$key but I find that the {} at least help somewhat at showing what exactly goes on here
					$data->{$key} = $newData;
				} else {
					$data[$key]   = $newData;
				}
			}
		} else {
			//it is something that we can escape directly, lets do it
			$data = html_escape($data);
		}
		//whatever the data was that we where given, it is clean now. Lets return it.
		return $data;
	}
	public function outputPlusFilter($data){
		$data = $this->escapeData($data);
		$this->output->set_output(json_encode($data));
		return $this->output;
	}
	
	//this function is used if a resource is made to uniformaly return stuff to the client
	public function niceMade($errored,$urlPart,$resourceKind="",$resourceName="",$pref=3,$correctReturn=201){
		if($errored!=RP_ERROR_NONE){
			if($errored=RP_ERROR_DUPLICATE){
				$this->output->set_header(409);
				$body = [
					"messages" => [
						"One or more given values are already in use.",
						"[NAME] is already in use",
						"The given [VALUE] is already in use"
					],
					"name"  => $resourceName,
					"VALUE" => $resourceKind,
					"pref"  => $pref
				];
				$this->outputPlusFilter($body)->_display();
				die();
			//expand possible errors here
			} elseif($errored==RP_ERROR_GENERIC) {
				//a generic error happened :(
				$this->output->set_header(500);
				$this->outputPlusFilter(["message"=>"Something broke, please retry later.", "errorCode"=>$errored])->_display();
				die();
			} else {
				//something very weird happened....
				//a generic error happened :(
				$this->output->set_header(500);
				$this->outputPlusFilter(["message"=>"This shouldn't have happened..... :('","errorCode"=>$errored])->_display();
			}
			//something went wrong... :(
			//TODO implement something went wrong code
		} else {
		$body = [
				//this contains some nice messages that can be displayed to the user if the client wishes to stay on the same page
				"messages" => [
					"The item is successfully created",
					"The ".$resourceKind." is successfully created",
					$resourceName." is successfully created",
				],
				//same as the location header.
				"link" => base_url("index.php/api/".$urlPart),
				"name" => $resourceName,
				"kind" => $resourceKind,
				"pref" => $pref
			];
			if($correctReturn==201){
				$this->output->set_header("Location: ".$body["link"]);
			}
			$this->output->set_status_header($correctReturn);
			unset($body["link"]);
			$this->outputPlusFilter($body)->_display();
			die();
		}
	}
	public function niceReturn($data,$responce=200,$allowOverwrite=true,$extraText=null,$die=true){
		if(empty($data)){
			if($allowOverwrite){
				$responce = 404;
			}
		}
		$this->output->set_status_header($responce,$extraText);
		$this->outputPlusFilter($data);
		if($die){
			$this->output->_display();
			die();
		}
	}
}
	
