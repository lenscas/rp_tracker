<?php
class Users extends API_Parent {
	public function __construct(){
		parent::__construct(false);
		$this->load->model("Users_model");
	}
	public function login(){
		parent::redirectLoggedIn();
		$error;
		$this->load->library('form_validation');
		$this->form_validation->set_rules("username","username","required");
		$this->form_validation->set_rules("password","password","required");
		if($this->form_validation->run()){
			$error=$this->Users_model->login(parent::getPostSafe());
		} else {
			$error="One or more fields are not filled in.";
		}
		$code=200;
		if($error){
			$code=422;
		}
		parent::niceReturn(["loggedIn"=>!$error,"error"=>$error],$code,false);
	}
	public function register(){
		//make sure the user is not logged in
		parent::redirectLoggedIn();
		$error;
		$success=false;
		//data that needs to be included in the request.
		$checkOn =[
			["username","username","required"],
			["password","password","required|matches[passwordCheck]"],
			["passwordCheck","passwordCheck","required"],
			["mail","email","required|valid_email"]
		];
		//get the post data if the request contained everything we needed. Else we exit and give the user the correct error
		$data = parent::checkAndErr($checkOn,false,false);
		//register the user and check if there where any more errors
		$status=$this->Users_model->register($data);
		if($status["error"]){
			//there was at least 1 error. Time to throw it to the user
			parent::niceMade($status[0]["error"],"",$status["on"],$status["value"]);
		} else {
			parent::niceMade(0,"","user","account");
		}
		//parent::niceReturn(["success"=>!$error,"error"=>$error],$code,false);
	}
	//simple function to include a link to all rp's in a list.
	private function createLinksForRPs($RPs){
		foreach($RPs as $key=>$value){
			$RPs[$key]->link = base_url("index.php/api/rp".$value->code);
		}
		return $RPs;
	}
	public function profile($userId=false){
		if(! $userId){
			$userId	=	parent::getIdForced();
		} else {
			parent::forceLogin();
		}
		//lets slowly get all the data that we need
		//we first need to grab another model
		$this->load->model("Rp_model");
		//first all rps made by this guy
		$madeRPs = $this->Rp_model->getAllRPFromUser($userId);
		$madeRPs = $this->createLinksForRPs($madeRPs);
		//now, all the rps this guy has joined
		$joinedRPs = $this->Rp_model->getAllJoinedRp($userId);
		$joinedRPs = $this->createLinksForRPs($joinedRPs);
		//other data that may or may not be important
		$userData = $this->Users_model->getUserData($userId);
		//now, lets put it in a nice array and send it to the user
		echo parent::niceReturn(["joinedRPs"=>$joinedRPs,"madeRPs"=>$madeRPs,"userData"=>$userData],200,false);
	}

}
