<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Battle extends RP_Parent {
	public function __construct(){
		parent::__construct();
		//$this->load->model("Battle_model");
	}
	public function create($rpCode){
		$this->load->model("Rp_model");
		$rpId=$this->Rp_model->getRPByCode($rpCode);
		if($this->Rp_model->checkIfGM($this->userId,$rpId)){
			parent::loadAll("battle/create",array("rpCode"=>$rpCode));
		}
		
	}

}
