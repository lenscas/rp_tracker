<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Char extends RP_Parent {
	public function __construct(){
		parent::__construct();
	}
	
	public function createChar($rpCode){
		$showForm=true;
		$this->load->library('form_validation');
		$this->form_validation->set_rules("name","name","required");
		/*$this->form_validation->set_rules("health","health","required|integer");
		$this->form_validation->set_rules("armour","armour","required|integer");
		$this->form_validation->set_rules("strength","strength","required|integer");
		$this->form_validation->set_rules("accuracy","accuracy","required|integer");
		$this->form_validation->set_rules("magicalSkill","magicalSkill","required|integer");
		$this->form_validation->set_rules("magicalDefence","magicalDefence","required|integer");*/
		if($this->form_validation->run()){
			$this->load->model("Character_model");
			$data=$this->Character_model->creatCharacter($this->userId,$rpCode,parent::getPostSafe());
			if($data['success']){
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
				redirect("rp/character/view/".$data['data']['code']);
			}
			
		}
		//echo validation_errors();
		if($showForm){
			parent::loadAll("char/create",$rpCode,array("rpCode"=>$rpCode));
		}
	}
	public function character($charCode){
		$this->load->model("Character_model");
		
		parent::loadAll("char/view",$this->Character_model->getRPCodeByChar($charCode),array("charCode"=>$charCode));
	}
	public function charList($rpCode){
		parent::loadAll("char/list",$rpCode,array("rpCode"=>$rpCode));
	}
}


?>
