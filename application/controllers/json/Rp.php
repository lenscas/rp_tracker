<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rp extends RP_Parent {
	public function __construct(){
		parent::__construct();
	}
	public function create(){
		$data=array("success"=>false);
		$this->load->library('form_validation');
		$this->form_validation->set_rules("name","name","required");
		$this->form_validation->set_rules("startingStatAmount","startingStatAmount","required|integer");
		$this->form_validation->set_rules("startingAbilityAmount","startingAbilityAmount","required|integer");
		$this->form_validation->set_rules("description","descripton","required");
		if($this->form_validation->run() ){
			$this->load->model("Rp_model");
			$data=$this->Rp_model->create($this->userId,$this->input->post());
		}
		echo json_encode($data);
	}
}
