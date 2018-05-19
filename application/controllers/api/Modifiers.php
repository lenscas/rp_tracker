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
	private function checkModIdRPCodeAndCharCode($rpCode,$charCode,$modId){
		$ids = $this->Modifiers_model->getModifierIds($modId);
		if(! $ids){
			return RP_ERROR_NOT_FOUND;
		}
		if($rpCode !== $ids->rpCode){
			return RP_ERROR_NOT_FOUND;
		}
		if($charCode !== $ids->charCode){
			return RP_ERROR_NOT_FOUND;
		}
		return RP_ERROR_NONE;
	}
	public function updateModifier($rpCode,$charCode,$modId){
		$data =  parent::checkAndErr($this->rules);
		$this->load->model("Rp_model");
		$rpId = $this->Rp_model->rpCodeToId($rpCode);
		$userId = $this->Modifiers_model->getUserIdByMod($modId);
		$alertData = array();
		if($userId && $rpId){
			$allowed = $userId === $this->userId;
			$allowed = $allowed || $this->Rp_model->checkIfGM($this->userId,$rpId);
			if($allowed){
				$this->Modifiers_model->updateModifier($modId,$data);
				$status = RP_ERROR_NONE;
				if($this->userId !== $userId){
					$alertData["vars"] = [
						"CHARCODE" => $charCode,
						"RP_CODE"  => $rpCode
					];
					$alertData["users"] = [$userId];
					$alertData["type"]  = "mod_change";
				}
			}else {
				$status = RP_ERROR_NO_PERMISSION;
			}
		} else {
			$status = RP_ERROR_NOT_FOUND;
		}
		parent::niceMade([
			"url" => "rp/".$rpCode."/characters/".$charCode,
			"resourceKind" =>"Modifier",
			"resourceName" =>$data["name"],
			"code" =>200,
			"status"=> $status,
			"alertData" => $alertData
		]);
	}
	public function insertModifier($rpCode,$charCode){
		$rules = $this->rules;
		$rules[] = ["intName","intName","required"];
		$data = parent::checkAndErr($rules);

		$this->load->model("Character_model");
		$this->load->model("Rp_model");
		$rpId = $this->Rp_model->rpCodeToId($rpCode);

		$status = RP_ERROR_NO_PERMISSION;
		$alertData = array();
		if($rpId && $this->Rp_model->checkIfGM($this->userId,$rpId)){

			$character=$this->Character_model->getCharacter($charCode,true,true);
			if($character){
				$data["rpId"] = $rpId;
				$modId=$this->Modifiers_model->insertModifier($data,$character->id);
				$status = RP_ERROR_NONE;
				if(!$modId){
					$status = RP_ERROR_GENERIC;
				} else {
					$alertData = [
						"type"  => "mod_change",
						"users" => [$character->userId],
						"vars"  => [
							"CHARCODE" => $charCode,
							"RP_CODE"  => $rpCode
						]
					];
				}
			} else {
				$status = RP_ERROR_NOT_FOUND;
			}
		} else{
			if(!$rpId){
				$status = RP_ERROR_NOT_FOUND;
			}
		}
		parent::niceMade([
			"url"          => "rp/".$rpCode."/characters/".$charCode,
			"resourceKind" => "Modifier",
			"resourceName" => $data["name"],
			"status"       => $status,
			"id"           => $modId ?? null,
			"alertData"    => $alertData
		]);
	}
	public function deleteModifier($rpCode,$charCode,$modId){
		$this->load->model("Rp_model");
		$status = $this->checkModIdRPCodeAndCharCode($rpCode, $charCode,$modId);
		$alertData = array();
		if($status === RP_ERROR_NONE){
			$rpId = $this->Rp_model->rpCodeToId($rpCode);
			if($this->Rp_model->checkIfGM($this->userId,$rpId)){
				$amountDeleted = $this->Modifiers_model->delete($modId);
				if($amountDeleted){
					$status=RP_ERROR_NONE;
					$this->load->model("Character_model");
					$userId = $this->Character_model->charCodeToUserId($charCode,$rpCode);
					if($userId !== $this->userId){
						$alertData = [
							"vars" => [
								"RP_CODE"  => $rpCode,
								"CHARCODE" => $charCode
							],
							"type" => "mod_change",
							"users" => [$userId],
						];
					}
				} else {
					$status = RP_ERROR_CONFLICT;
					$customError = "This is a base value and as such can not be deleted. Set it to 0 instead.";
				}
			} else {
				$status = RP_ERROR_NO_PERMISSION;
			}
		}
		parent::niceMade([
			"url" => "rp/".$rpCode."/characters/".$charCode,
			"resourceKind" =>"Modifier",
			"resourceName" => "",
			"status"=> $status,
			"code" =>200,
			"custErr" => $customError ?? null,
			"alertData" => $alertData
		]);
	}

}
