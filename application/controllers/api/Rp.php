<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rp extends API_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Rp_model");
	}
	public function create(){
		$data=array("success"=>false);
		$this->load->library('form_validation');
				$data=array("success"=>false);
		$this->load->library('form_validation');
		$checkOn = [
			["name","name","required"],
			["startingStatAmount","startingStatAmount","required|integer"],
			["startingAbilityAmount","startingAbilityAmount","required|integer"],
			["description","descripton","required"],
			["statSheetCode","statSheetCode","required"]
		];
		$data = parent::checkAndErr($checkOn);
		$rpData = $this->Rp_model->create($this->userId,$data);
		parent::niceMade(0,"rp/".$rpData["code"],"Roleplay",$data["name"]);
	}
	public function listAllRPs(){
		$data=$this->Rp_model->getAllRPs();
		parent::niceReturn($data);
	}
	public function getRP($rpCode){
		$isGM=$this->Rp_model->checkIfGM($this->userId,$rpCode);
		$rp=$this->Rp_model->getWholeRp($rpCode,$isGM);
		$result = $this->Rp_model->checkIfJoined($this->userId,$rp->id);
		$rp->isJoined = false;
		if($result){
			$rp->isJoined = true;
		}
		unset($rp->id);
		parent::niceReturn($rp);
	}
	public function join($rpCode){
		$rp=$this->Rp_model->getRPByCode($rpCode);
		if($rp){
			if($this->Rp_model->joinRp($this->userId,$rp->id)){
				parent::niceReturn(["success"=>true,"isJoined"=>true]);
			} else {
				parent::niceReturn(["success"=>false,"isJoined"=>true]);
			}
		} else {
			parent::niceReturn();
		}
		
	}
	
	public function getRPConfig($rpCode){
		$data = $this->Rp_model->getRPConfigByCode($rpCode,$this->userId);
		parent::niceReturn($data);
	}
	public function getAllStatSheets(){
		echo json_encode($this->Rp_model->getAllStatSheets());
	}
}
