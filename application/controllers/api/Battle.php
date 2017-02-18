<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Battle extends RP_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Battle_model");
		$this->load->model("Rp_model");
	}
	
	public function createBattle(){
		$this->load->library("form_validation");
		$this->form_validation->set_rules("name","name","required");
		$this->form_validation->set_rules("characters[]","characters","required");
		$this->form_validation->set_rules("rpCode","rpCode","required");
		if($this->form_validation->run()){
			//get all the data
			$data	=	parent::getPostSafe();
			//load the RP model so we can change the rpCode into the rpId
			$rp=$this->Rp_model->getRPByCode($data['rpCode']);
			if($this->Rp_model->checkIfGM($this->userId,$rp->id)){
				//prepare the post data to create the battle
				$characters=$data['characters'];
				unset($data['characters']);
				unset($data['rpCode']);
				$data['rpId']=$rp->id;
				//create the battle
				$battleId=$this->Battle_model->createBattle($data);
				echo $battleId;
				//now we want to get all the agility stats from all characters in the list. 
				$this->load->model("Modifiers_model");
				$charListWithAgil=$this->Modifiers_model->getTotalStat($characters,"evade_defense",true,true);
				//we then turn the agillity stat into an order.
				$charListWithOrder=$this->Battle_model->decideOrder($charListWithAgil,true,$battleId);
				$this->Battle_model->insertCharsInBattle($charListWithOrder,true);
				
			} else {
				echo json_encode(array("success"=>false,"error"=>"You have no permission to make a battle"));
			}
		} else {
			echo json_encode(array("success"=>false,"error"=>"one or more fields where not set correctly"));
			echo validation_errors();
		}
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
		echo json_encode(["battles"=>$battles,"tags"=>$tags]);
	}
	public function getBattle($rpCode,$battleId){
		
		$isGM = $this->Rp_model->checkIfGM($this->userId,$rpCode);
		$battleData= $this->Battle_model->getBattle($rpCode,$battleId,$isGM);
		echo json_encode($battleData);
	}

}
