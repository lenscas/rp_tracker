<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Modifiers extends API_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Modifiers_model");
	}
	private $rules = [
		["name","name","required"],
		["value","value","required|integer"],
		["countDown","countDown","required|integer"],
	];
	public function updateModifier($rpCode,$charCode,$modId){
		$data =  parent::checkAndErr($this->rules);
		$rpId = $this->Modifiers_model->getRPfromMod($modId);
		$this->load->model("Rp_model");
		
		$allowed = $this->Modifiers_model->getUserIdByMod($modId)===$this->userId; 
		$allowed = $allowed || $this->Rp_model->checkIfGM($this->userId,$rpId);
		if($allowed){
			$this->Modifiers_model->updateModifier($modId,$data);
			$status = RP_ERROR_NONE;
		}else {
			$status = RP_ERROR_NO_PERMISSION;
		}
		parent::niceMade([
			"url" => "rp/".$rpCode."/characters/".$charCode,
			"resourceKind" =>"Modifier",
			"resourceName" =>$data["name"],
			"code" =>200,
			"status"=> $status
		]);
	}
	public function insertModifier($rpCode,$charCode){
		$rules = $this->rules;
		$rules[] = ["intName","intName","required"];
		$data = parent::checkAndErr($rules);
		$this->load->model("Character_model");
		$rpId = $this->Character_model->getRPIdByChar($charCode);
		$this->load->model("Rp_model");
		$status = RP_ERROR_NO_PERMISSION;
		if($this->Rp_model->checkIfGM($this->userId,$rpId->id)){
			$character=$this->Character_model->getCharacter($charCode,true,true);
			$data["rpId"] = $rpId->id;
			$modId=$this->Modifiers_model->insertModifier($data,$character->id);
			$status = RP_ERROR_NONE;
			if(!$modId){
				$status = RP_ERROR_GENERIC;
			}
		}
		parent::niceMade([
			"url" => "rp/".$rpCode."/characters/".$charCode,
			"resourceKind" =>"Modifier",
			"resourceName" =>$data["name"],
			"status"=> $status
		]);
	}
	public function deleteModifier($rpCode,$charCode,$modId){
		$rpId = $this->Modifiers_model->getRPfromMod($modId);
		$this->load->model("Rp_model");
		$status = RP_ERROR_NO_PERMISSION;
		if($this->Rp_model->checkIfGM($this->userId,$rpId)){
			$amountDeleted = $this->Modifiers_model->delete($modId);
			if($amountDeleted){
				$status=RP_ERROR_NONE;
			} else {
				$status = RP_ERROR_CONFLICT;
				$customError = "This is a base value and as such can not be deleted. Set it to 0 instead.";
			}
		}
		parent::niceMade([
			"url" => "rp/".$rpCode."/characters/".$charCode,
			"resourceKind" =>"Modifier",
			"resourceName" => "",
			"status"=> $status,
			"code" =>200,
			"custErr" => $customError ?? null
		]);
	}

}
