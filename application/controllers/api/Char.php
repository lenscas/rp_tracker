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


}
?>
