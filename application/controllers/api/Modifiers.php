<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Modifiers extends RP_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Modifiers_model");
	}
	public function updateModifier($modId){
		
		if($this->checkIfValidMod(true)){
			$this->load->model("Rp_model");
			$rpId	=	$this->Modifiers_model->getRPfromMod($modId);
			if($this->Rp_model->checkIfGM($this->userId,$rpId)){
				$this->Modifiers_model->updateModifier($modId,parent::getPutSafe());
				echo json_encode(array("success"=>true));
			}else {
				echo json_encode(array("success"=>false,"error"=>"You don't have permission to edit this."));
			}
			
		} else {
			var_dump(parent::getPostSafe());
			echo json_encode(array("success"=>false,"error"=>"One or more fields are not set correctly."));
		}
	}
	public function insertModifier($charCode){
		if($this->checkIfValidMod()){
			$this->load->model("Character_model");
			$rpId	=	$this->Character_model->getRPIdByChar($charCode);
			$this->load->model("Rp_model");
			if($this->Rp_model->checkIfGM($this->userId,$rpId)){
				$character=$this->Character_model->getCharacter($charCode,true);
				$modId=$this->Modifiers_model->insertModifier(parent::getPostSafe(),$character['id']);
				echo json_encode(array("success"=>true,"id"=>$modId));
			} else {
				echo json_encode(array("success"=>false,"error"=>"You don't have permission to create this.'"));
			}
		}else{
			echo json_encode(array("success"=>false,"error"=>"One or more fields are not set correctly."));
		}
	}
	private function checkIfValidMod($isPut=false){
		$this->load->library("form_validation");
		if($isPut){
			$this->form_validation->set_data(parent::getPut());
		}
		$this->form_validation->set_rules("name","name","required");
		$this->form_validation->set_rules("value","value","required|integer");
		$this->form_validation->set_rules("countDown","countDown","required|integer");
		return $this->form_validation->run();
	}
	public function deleteModifier($modId){
		$rpId	=	$this->Modifiers_model->getRPfromMod($modId);
		$this->load->model("Rp_model");
		if($this->Rp_model->checkIfGM($this->userId,$rpId)){
			$amountDeleted	=	$this->Modifiers_model->delete($modId);
			if($amountDeleted){
				echo json_encode(array("success"=>true));
			}else {
				echo json_encode(array("success"=>false,"error"=>"This is a base value and as such can not be deleted. Set it to 0 instead."));
			}
		} else {
			echo json_encode(array("success"=>false,"error"=>"You don't have permission to delete this'"));
		}
	}
}
