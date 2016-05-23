<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends User_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Users_model");
	}
	public function login(){
		parent::redirectLoggedIn();
		$error;
		if($this->input->post()){
			$error=$this->Users_model->login($this->input->post());
		}
		if(!$error){
			echo json_encode(array("loggedIn"=>true));
		} else {
			echo json_encode(array("loggedIn"=>false,"error"=>$error));
		}
	}
	public function register(){
		$error;
		$success=false;
		parent::redirectLoggedIn();
		if($this->input->post()){
			$error=$this->Users_model->register($this->input->post());
		} else {
			$error="No post data found";
		}
		if(!$error){
			$success=true;
		}
		echo json_encode(array("error"=>$error,"success"=>$success));
	}
	public function profile($userId){
		echo json_encode($this->Users_model->getUserData($userId));
	}

}
