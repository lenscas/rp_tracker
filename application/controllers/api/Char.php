<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Char extends RP_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Character_model");
	}
	public function getCharacter($charCode){
		$data=array();
		$data=$this->Character_model->getCharacter($charCode);
		if($data['success']){
			$this->load->model("Character_model");
			$data['abilities']=$this->Character_model->getAbilitesFromCharCode($charCode);
		}
		echo json_encode($data);
	}
	public function getCharList($rpCode){
		$data=$this->Character_model->getCharListByRPCode($rpCode);
		echo json_encode($data);
	}
	public function getAbilitiesByCharInRP($rpCode){
		$data=$this->Character_model->getAbilitiesByCharInRP($rpCode);
		echo json_encode($data);
	}


}
?>
