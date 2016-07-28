<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rp extends RP_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Rp_model");
	}
	public function create(){
		$data=array("success"=>false);
		$this->load->library('form_validation');
		$this->form_validation->set_rules("name","name","required");
		$this->form_validation->set_rules("startingStatAmount","startingStatAmount","required|integer");
		$this->form_validation->set_rules("startingAbilityAmount","startingAbilityAmount","required|integer");
		$this->form_validation->set_rules("description","descripton","required");
		$this->form_validation->set_rules("statSheetCode","statSheetCode","required");
		if($this->form_validation->run() ){
			$data=$this->Rp_model->create($this->userId,parent::getPostSafe());
		}
		echo json_encode($data);
	}
	public function listAllRPs(){
		$data=$this->Rp_model->getAllRPs();
		echo json_encode($data);
	}
	public function getRP($rpCode){
		$rp=$this->Rp_model->getWholeRp($rpCode);
		unset($rp->id);
		echo json_encode($rp);
	}
	public function join($rpCode){
		$rp=$this->Rp_model->getRPByCode($rpCode);
		if($rp){
			if($this->Rp_model->joinRp($this->userId,$rp->id)){
				echo json_encode(array("success"=>true));
			} else {
				echo json_encode(array("success"=>false,"error"=>"Already Joined"));
			}
		} else {
			echo json_encode(array("success"=>false,"error"=>"Code is not a valid rp"));
		}
		
	}
	
	public function getRPConfig($rpCode){
		echo json_encode($this->Rp_model->getRPConfigByCode($rpCode));
	}
	public function getAllStatSheets(){
		echo json_encode($this->Rp_model->getAllStatSheets());
	}
}
