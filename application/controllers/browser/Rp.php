<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rp extends RP_Parent {
	public function __construct(){
		parent::__construct();
	}
	public function create(){
		parent::loadAll("rp/create");
	}
	public function createChar($rpCode){
		$showForm=true;
		$this->load->library('form_validation');
		$this->form_validation->set_rules("name","name","required");
		$this->form_validation->set_rules("health","health","required|integer");
		$this->form_validation->set_rules("armour","armour","required|integer");
		$this->form_validation->set_rules("strength","strength","required|integer");
		$this->form_validation->set_rules("accuracy","accuracy","required|integer");
		$this->form_validation->set_rules("magicalSkill","magicalSkill","required|integer");
		$this->form_validation->set_rules("magicalDefence","magicalDefence","required|integer");
		if($this->form_validation->run()){
			echo "The validation went correctly<br><pre>";
			$this->load->model("Rp_model");
			$data=$this->Rp_model->creatCharacter($this->userId,$rpCode,$this->input->post());
			print_r($data);
			echo "</pre>";
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
					$this->Rp_model->setPicture($data['charId'],$uploadData['file_name'],false);
				} 
			}
			
		}
		echo validation_errors();
		if($showForm){
			parent::loadAll("rp/character",array("rpCode"=>$rpCode));
		}
	}
	
}
