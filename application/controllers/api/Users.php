<?php
class Users extends User_Parent {
	public function __construct(){
		parent::__construct();
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
		if(!$error){
			echo json_encode(array("loggedIn"=>true));
		} else {
			echo json_encode(array("loggedIn"=>false,"error"=>$error));
		}
	}
	public function register(){
		parent::redirectLoggedIn();
		$error;
		$success=false;
		$this->load->library('form_validation');
		$this->form_validation->set_rules("username","username","required");
		$this->form_validation->set_rules("password","password","required");
		$this->form_validation->set_rules("passwordCheck","passwordCheck","required");
		$this->form_validation->set_rules("mail","email","required");
		if($this->form_validation->run()){
			$data=parent::getPostSafe(true);
			if($data['safe']){
				if($data['clean']['password']==$data['clean']['passwordCheck']){
					$error=$this->Users_model->register($data['clean']);
				} else {
					$error="Passwords don't match.";
				}
			} else {
				echo json_encode(array("error"=>"XSS detected. Please don't use html tags in your username, password or email.'", "success"=>false));
				die;
			}
		} else {
			$error="One or more fields are not filled in correctly.";
		}
		if(!$error){
			$success=true;
		}
		echo json_encode(array("error"=>$error,"success"=>$success));
	}
	public function profile($userId=false){
		if(! $userId){
			$userId	=	parent::getIdForced();
		}
		//lets slowly get all the data that we need
		//we first need to grab another model
		$this->load->model("Rp_model");
		//first all rps made by this guy
		$madeRPs = $this->Rp_model->getAllRPFromUser($userId);
		//now, all the rps this guy has joined
		$joinedRPs = $this->Rp_model->getAllJoinedRp($userId);
		//other data that may or may not be important
		$userData = $this->Users_model->getUserData($userId);
		//now, lets put it in a nice array and send it to the user
		echo json_encode(["success"=>true,"joinedRPs"=>$joinedRPs,"madeRPs"=>$madeRPs,"userData"=>$userData]);
	}

}
