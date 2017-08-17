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
			parent::loadAll("battle/create",$rpCode,array("rpCode"=>$rpCode));
		}
		
	}
	public function battleList($rpCode){
		//$rpId=$this->Rp_model->getRPByCode($rpCode);
		parent::loadAll("battle/list",$rpCode,array("rpCode"=>$rpCode));
	}
	public function manageBattle($battleId){
		$this->load->model("Battle_model");
		$rpCode = $this->Battle_model->getRPCodeByBattle($battleId);
		parent::loadAll("battle/manage",$rpCode,array("battleId"=>$battleId,"rpCode"=>$rpCode));
	}

}
