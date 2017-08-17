<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Char extends API_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Character_model");
	}
	public function getCharacter($rpCode,$charCode){
		$data=array();
		//we want to know if the user is a gm or not as this changes if we want to display hidden characters or not
		$this->load->model("Rp_model");
		$isGM = $this->Rp_model->checkIfGM($this->userId,$rpCode);
		$data=$this->Character_model->getCharacter($charCode,false,$isGM,$rpCode);
		if($data['success']){
			$this->load->model("Character_model");
			$data['abilities']=$this->Character_model->getAbilitesFromCharCode($charCode);
			$data["canEdit"]=false;
			if($data["character"]->userId==$this->userId || $isGM){
				unset($data["character"]->userId);
				$data["canEdit"]=true;
			}
		}
		echo json_encode($data);
	}
	public function getCharList($rpCode){
		$this->load->model("Rp_model");
		$isGM = $this->Rp_model->checkIfGM($this->userId,$rpCode);
		$data=$this->Character_model->getCharListByRPCode($rpCode,$isGM);
		echo json_encode($data);
	}
	public function getAbilitiesByCharInRP($rpCode){
		$this->load->model("Rp_model");
		$isGM =  $this->Rp_model->checkIfGM($this->userId,$rpCode);
		if($isGM){ //if the user is an GM we can just get all the abilities. This is quicker then getting all the abilities from characters that are not hidden
			$data=$this->Character_model->getAbilitiesByCharInRP($rpCode);
			
		} else {
			$charactersNoHidden = $this->Character_model->getCharListByRPCode($rpCode,$isGM);
			$data = $this->Character_model->getAbilitiesByCharList($charactersNoHidden["characters"]);
		}
		echo json_encode($data);
	}
	public function createCharacter($rpCode){
		$checkOn = [
			["name","name","required"],
			["age","age","required|integer"],
			["backstory","backstory","required"],
			["personality","personality","required"]
		];
		$postData=parent::checkAndErr($checkOn);
		//$this->load->model("Character_model");
		$data=$this->Character_model->creatCharacter($this->userId,$rpCode,$postData);
		parent::niceMade($data["success"],"rp/".$rpCode."/characters/".$data["code"],"character",$postData["name"]);
	}
	//not needed anymore now that we are going to use php7
	private function easyArrayAccess($array,$field,$default=null){
		if(isset($array[$field])){
			return $array[$field];
		}
		echo "remove call to easyArrayAccess!";
		return $default;
	}
	public function patchCharacter($rpCode,$charCode){
		$data = $this->getPutSafe();
		$isGM = false; //this gets changed by checkIfUserMayEdit() to reflect if the user is an GM or not
		$error = RP_ERROR_NONE;
		$name = $charCode;
		$resourceKind = "character";
		$urlPart = "rp/" . $rpCode . "/characters/";
		try {
			if(!$this->Character_model->checkIfUserMayEdit($rpCode,$charCode,$this->userId,$isGM)){
				$error = RP_ERROR_NO_PERMISSION;
			}
			
		} catch (Exception $e) {
			if($e->getMessage()=="Character does not exist"){
				$error = RP_ERROR_NOT_FOUND;
				return;
			} else {
				throw $e;
			}
			
		}
		if($error!=RP_ERROR_NONE){
			$name = $charCode;
			if($error != RP_ERROR_NOT_FOUND){
				$urlPart = $urlPart . $charCode;
				$name = $this->Character_model->getCharacter($charCode,true,$isGM,$rpCode);
			}
			parent::niceMade($error,$urlPart,"character",$name);
		}
		$urlPart = $urlPart . $charCode;
		$stats						=	$data["stats"] ?? null;
		$abilities					=	$data["abilities"] ?? null;
		$character					=	[
			"name"					=>	$data["name"] ?? false,
			"age"					=>	$data["age"] ?? false,
			"appearancePicture"		=>	$data["appearancePicture"] ?? false,
			"appearanceDescription"	=>	$data["appearanceDescription"] ?? false,
			"backstory"				=>	$data["backstory"] ?? false ,
			"personality"			=>	$data["personality"] ?? false,
			"notes"					=>	$data["notes"] ?? false,
			"hiddenData"			=>	$data["hiddenData"] ?? false
		];
		foreach($character as $key=>$value){
			if($value===false){
				unset($character[$key]);
			}
		}
		if(!empty($character)){
			$this->Character_model->updateCharacter($charCode,$character,true);
		}
		if($stats){
			$this->load->model("Modifiers_model");
			$error = $this->Modifiers_model->updateBaseStats($stats);
		}
		if($abilities && $error == RP_ERROR_NONE){
			$error = $this->Character_model->updateAbilities($charCode,$abilities,$isGM,true);
		}
		parent::niceMade(RP_ERROR_NONE,$urlPart,"character",$name,3,200);
	}
}
