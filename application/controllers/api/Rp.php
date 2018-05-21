<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rp extends API_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Rp_model");
	}
	private function checkEmpty($array){
		$allEmpty = true;
		foreach($array as $key2=>$value2){
			if($value2){
				$allEmpty = false;
				break;
			}
		}
		return $allEmpty;
	}
	private function checkIfValidList($array,$rules){
		$array = $array ?? array();
		foreach($array as $key=>$value){
			if($this->checkEmpty($array[$key])){
				unset($array[$key]);
			} else {
				parent::checkAndErr($rules,$value);
			}
		}
		return $array;
	}
	public function create(){
		$checkOn = [
			["name","name","required"],
			["startingStatAmount","startingStatAmount","required|integer"],
			["startingAbilityAmount","startingAbilityAmount","required|integer"],
			["description","description","required"],
			["battleSystem","battleSystem","required"]
		];
		$data = parent::checkAndErr($checkOn);
		$rules = [
			["name","name","required"],
			["internalName","internalName","required"]
		];
		$data["statList"] = $this->checkIfValidList(
			$data["statList"] ?? array(),
			$rules
		);
		$rules = [
			["name","name","required"],
			["code","code","required"]
		];
		$data["actionList"] = $this->checkIfValidList(
			$data["actionList"] ?? array(),
			$rules
		);
		$rpData = $this->Rp_model->create($this->userId,$data);
		parent::niceMade([
			"url"          => "rp/".$rpData["code"],
			"resourceKind" => "Roleplay",
			"resourceName" => $data["name"],
			"id"           => $rpData["code"],
			"status"       => RP_ERROR_NONE,
		]);
	}
	public function listAllRPs(){
		$data=$this->Rp_model->getAllRPs();
		parent::niceReturn($data);
	}
	public function getRP($rpCode){
		$isGM=$this->Rp_model->checkIfGM($this->userId,$rpCode);
		$rp=$this->Rp_model->getWholeRp($rpCode,$isGM);
		if(!$rp){
			parent::niceReturn(array());
		}
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
				$sendTo = $this->Rp_model->getUsersInRp($rp->id,[$this->userId]);
				$alertData = [
					"users"=>$sendTo,
					"type"=>"new_player",
					"vars"=>[
						"RP_CODE"=>$rpCode,
						"USERID"=>$this->userId,
					]
				];
				parent::niceReturn(
					["success"=>true,"isJoined"=>true],
					["alertData"=>$alertData]
				);
			} else {
				parent::niceReturn(["success"=>false,"isJoined"=>true]);
			}
		} else {
			parent::niceReturn(array());
		}

	}

	public function getRPConfig($rpCode){
		$data = $this->Rp_model->getRPConfigByCode($rpCode,$this->userId);
		parent::niceReturn($data);
	}
}
