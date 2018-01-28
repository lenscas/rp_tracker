<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Battle extends API_Parent {
	public function __construct(){
		parent::__construct(true,true);
		$this->load->model("Battle_model");
		$this->load->model("Rp_model");
	}
	public function getAllBattleSystems(){
		parent::forceLogIn();
		$this->load->model("Stat_model");
		$this->load->model("Action_model");
		$battleSystems = $this->Battle_model->getAllBattleSystems();
		$battleSystemsWithStats = $this->Stat_model->getAllDefaultStatsBySystems($battleSystems);
		$battleSystemStatsAndActions = $this->Action_model->getAllDefaultActionsBySystems($battleSystemsWithStats);
		parent::niceReturn($battleSystemStatsAndActions,["die"=>true]);
	}
	public function createBattle($rpCode){
		parent::forceLogIn();
		$rules = [
			["name","name","required"],
			["characters[]","characters","required"],
		];
		$data = parent::checkAndErr($rules);
		$rp=$this->Rp_model->getRPByCode($rpCode);
		$error = RP_ERROR_NO_PERMISSION;
		if($this->Rp_model->checkIfGM($this->userId,$rp->id)){
			//prepare the post data to create the battle
			$characters=$data['characters'];
			unset($data['characters']);
			//create the battle
			$battleId=$this->Battle_model->createBattle($rp->id,$data);
			$this->Battle_model->insertCharsInBattle($battleId,$rpCode,$characters);
			$error = RP_ERROR_NONE;
		}
		parent::niceMade($error,"rp/".$rpCode."/battles".$battleId,"Battle",$data["name"]);
	}
	public function getAllBattlesByRp($rpCode){
		parent::forceLogIn();
		$battles	=	$this->Battle_model->getAllBattles($rpCode,true);
		$charList	=	$this->Battle_model->getAllCharsInBattle($rpCode,true);
		$this->load->model("Tag_model");
		$tags = $this->Tag_model->getAllTagsByCharList($charList);
		//we need to strip away the character code from all characters if the user is not a GM
		$isGM		=	$this->Rp_model->checkIfGM($this->userId,$rpCode);
		if(!$isGM){
			$res = $this->Tag_model->removeAllHiddenFromCharList($charList,$tags);
			//var_dump($res);
			$tags=$res["tags"];
			$charList = $res["characters"];
		}
		foreach($battles as $battleKey=>$battleValue){
			$battles[$battleKey]['characters']=array();
			foreach($charList as $charKey=>$charValue){
				if($charValue->battleId==$battleValue['id']){
					$battles[$battleKey]['characters'][]=$charList[$charKey];
					unset($charList[$charKey]);
				}
			}
		}
		parent::niceReturn(["battles"=>$battles,"tags"=>$tags]);
	}
	public function getBattle($rpCode,$battleId){
		parent::forceLogIn();
		$isGM = $this->Rp_model->checkIfGM($this->userId,$rpCode);
		$battleData= $this->Battle_model->getBattle($rpCode,$battleId,$isGM);
		parent::niceReturn($battleData);
	}
	public function getAllUsersInBattle($rpCode,$battleId){
		parent::forcePadServer();
		parent::niceReturn($this->Battle_model->getAllUsersInBattle($rpCode,$battleId));
		
	}
	public function saveDeltas($rpCode,$battleId){
		parent::forceLogin();
		$data = parent::getPut();
		if(!$this->usedJSON){
			parent::niceMade([
				"status"  => RP_ERROR_NOT_PROCESSABLE,
				"custError" => "Use JSON instead of a query string to send data. php may truncate some of the data otherwise."
			]);
		}
		$rpId = $this->Rp_model->rpCodeToId($rpCode);
		$isGM = $this->Rp_model->checkIfGM($rpId,$battleId);
		$isBattleFromRP = $this->Battle_model->checkIfBattleInRP($rpId,$battleId);
		if($isBattleFromRP){
			$this->load->model("Action_model");
			
			$error = $this->Action_model->saveDeltas($data,$battleId,$rpId);
			$data = array();
			if($error["error"]){
				$data["status"] =RP_ERROR_GENERIC;
			} else {
				$data["status"] = RP_ERROR_NONE;
			}
			$data["code"] = 200;
			$data["resourceKind"] = "Battle Enviroment";
			$data["resourceName"] = "Battle Enviroment";
			$data["url"]          = "/rp/".$rpCode."/battles/".$battleId;
			if($error["message"] ?? false){
				$data["custError"] = $error["message"];
			}
			parent::niceMade($data);
		} else {
			parent::niceMade([
				"status" => RP_ERROR_NO_PERMISSION,
				"url"    => "/rp/".$rpCode."/battles/".$battleId
				
			]);
		}
	}

}
