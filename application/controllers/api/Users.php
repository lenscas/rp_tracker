<?php
class Users extends API_Parent {
	public function __construct(){
		parent::__construct("Users_model", false);
	}
	public function login(){
		$this->user->redirectLoggedIn();
		$error;
		$checkOn = [
			["username","username","required"],
			["password","password","required"]
		];
		$data  = $this->body->checkAndErr($checkOn);

		$error = $this->Users_model->login($data);
		$this->setOutput->add("loggedIn",true)->add("userId", $this->session->userId)->render();
	}
	public function logout(){
		$this->session->sess_destroy();
		parent::niceReturn(["success"=>true]);
	}
	public function register(){
		//make sure the user is not logged in
		$this->user->redirectLoggedIn();
		$error;
		$success=false;
		//data that needs to be included in the request.
		$checkOn =[
			["username","username","required|is_unique[users.username]"],
			["password","password","required|matches[passwordCheck]"],
			["passwordCheck","passwordCheck","required"],
			["mail","email","required|valid_email|is_unique[users.email]"]
		];
		//get the post data if the request contained everything we needed. Else we exit and give the user the correct error
		$data = $this->body->checkAndErr($checkOn);
		//register the user and check if there where any more errors
		$this->Users_model->register($data);
		$this->setOutput->setCode(Output::CODES["CREATED"])->render();
		//parent::niceReturn(["success"=>!$error,"error"=>$error],$code,false);
	}
	//simple function to include a link to all rp's in a list.
	private function createLinksForRPs($RPs){
		foreach($RPs as $key=>$value){
			$RPs[$key]->link = base_url("index.php/api/rp".$value->code);
		}
		return $RPs;
	}
	public function getName($userId){
		parent::forceLogIn();
		$userName = $this->Users_model->getUserName($userId);
		parent::niceReturn($userName);
	}
	public function profile($userId=false){
		if(! $userId){
			$userId	=	$this->user->getIdForced();
		} else {
			$this->user->forceLogin();
		}
		$userData = $this->Users_model->getUserData($userId);
		if(!$userData){
			parent::niceReturn([],404,false);
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
		//now, lets put it in a nice array and send it to the user
		$this->setOutput->add("joinedRPS",$joinedRPs)
			->add("madeRPs",$madeRPs)
			->add("userData",$userData)
			->render();
	}

}
