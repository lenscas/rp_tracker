<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Battle extends API_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Battle_model");
		$this->load->model("Rp_model");
	}
	
	public function createBattle($rpCode){
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
		
		$isGM = $this->Rp_model->checkIfGM($this->userId,$rpCode);
		$battleData= $this->Battle_model->getBattle($rpCode,$battleId,$isGM);
		parent::niceReturn($battleData);
	}

}
