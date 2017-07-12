<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Char extends RP_Parent {
	public function __construct(){
		parent::__construct();
	}
	
	public function createChar($rpCode){
		parent::loadAll("char/create",$rpCode,array("rpCode"=>$rpCode));
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
