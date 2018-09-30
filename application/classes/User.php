<?php
class User {
	protected $session;
	protected $output;
	protected $config;
	public function __constructor(){
		$CI = &get_instance();
		$this->session = $CI->session;
		$this->output = $CI->output;
		$this->config = $CI->config;
	}
	protected function block(){
		$this->output->set_status_header(403)->_display();
		die();
	}
	public function checkIsLoggedIn(){
		return $_SESSION["userId"] ?? false;
	}
	//used to force a login
	public function forceLogIn(){
		if(!$this->checkIsLoggedIn()){
			$this->block();
		}
	}
	public function getIdForced(){
		$this->forceLogIn();
		return $_SESSION["userId"];
	}
	public function redirectLoggedIn(){
		if($_SESSION["userId"] ?? false){
			redirect("api/users/".$this->getIdForced());
			echo json_encode(["userId"=> $_SESSION["userId"]]);
			die();
		}
	}
	private function checkIsPad(){
		$this->config->load("socket");
		$header = $this->input->get_request_header("pad_token");
		return $header === $this->config->item("pad_token");
	}
	public function forcePadServer(){
		if(!$this->checkIsPad()){
			http_response_code(422);
			echo json_encode(["message"=>"token missmatch"]);
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