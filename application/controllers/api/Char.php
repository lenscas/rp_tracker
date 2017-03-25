<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Char extends RP_Parent {
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
		$this->load->library('form_validation');
		$this->form_validation->set_rules("name","name","required");
		$this->form_validation->set_rules("age","age","required|integer");
		$this->form_validation->set_rules("backstory","backstory","required");
		$this->form_validation->set_rules("personality","personality","required");
		if($this->form_validation->run()){
			//$this->load->model("Character_model");
			$data=$this->Character_model->creatCharacter($this->userId,$rpCode,parent::getPostSafe());
			if($data['success']){
				if(!$data["hasGivenURL"]){
					$showForm=false;
					$config['upload_path']	= './assets/uploads/characters';
					$config['allowed_types']= 'gif|jpg|png';
					$config['max_size']		= '2048';
					$config['max_width']	= '0';
					$config['max_height']	= '0';
					$config['remove_spaces']=true;
					$this->load->library('upload', $config);
					if ($this->upload->do_upload("appearancePicture")){
						$uploadData=$this->upload->data();
						$this->Character_model->setPicture($data['data']['charId'],$uploadData['file_name'],false);
					}
				}
				echo json_encode(["success"=>true,"error"=>false,"charCode"=>$data["data"]["code"]]);
				//redirect("rp/character/view/".$data['data']['code']);
			}
			
		} else {
			echo json_encode(["success"=>false,"error"=>"some or more fields where not filled in","data"=>parent::getPostSafe()]);
		}
	}
	private function easyArrayAccess($array,$field,$default=null){
		if(isset($array[$field])){
			return $array[$field];
		}
		return $default;
	}
	public function patchCharacter($rpCode,$charCode){
		$data = $this->getPutSafe();
		$isGM = false;
		try {
			$mayEdit = $this->Character_model->checkIfUserMayEdit($rpCode,$charCode,$this->userId,$isGM);
		} catch (Exception $e) {
			if($e->getMessage()=="Character does not exist"){
				show_404();
				return;
			} else {
				throw $e;
			}
			
		}
		if(!$mayEdit){
			echo json_encode(["error"=>"You are not allowed to change this character","success"=>false]);
			return;
		}
		$stats						=	$this->easyArrayAccess($data,"stats");
		$abilities					=	$this->easyArrayAccess($data,"abilities");
		$character					=	[
			"name"					=>	$this->easyArrayAccess($data,"name",false),
			"age"					=>	$this->easyArrayAccess($data,"age",false),
			"appearancePicture"		=>	$this->easyArrayAccess($data,"appearancePicture",false),
			"appearanceDescription"	=>	$this->easyArrayAccess($data,"appearanceDescription",false),
			"backstory"				=>	$this->easyArrayAccess($data,"backstory",false),
			"personality"			=>	$this->easyArrayAccess($data,"personality",false),
			"notes"					=>	$this->easyArrayAccess($data,"notes",false),
			"hiddenData"			=>	$this->easyArrayAccess($data,"hiddenData",false)
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
			$res = $this->Modifiers_model->updateBaseStats($stats, $isGM);
			if(!$res["success"]){
				echo json_encode($res);
				return;
			}
		}
		if($abilities){
			$res = $this->Character_model->updateAbilities($charCode,$abilities,$isGM,true);
			if(!$res["success"]){
				echo json_encode($res);
				return;
			}
		}
		echo json_encode(["success"=>true]);
	}
}
