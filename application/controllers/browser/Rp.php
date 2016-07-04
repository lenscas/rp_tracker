<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rp extends RP_Parent {
	public function __construct(){
		parent::__construct();
	}
	public function create(){
		parent::loadAll("rp/create");
	}
	
	public function showAllRPs(){
		parent::loadAll("rp/rpList");
	}
	public function getRpDetails($rpCode){
		$this->load->model("Rp_model");
		$rp=$this->Rp_model->getRPByCode($rpCode);
		if($rp){
			$joined=$this->Rp_model->checkIfJoined($this->userId,$rp->id);
			$data=array("rpCode"=>$rpCode,"joined"=>$joined);
		} else {
			$data=array("exist"=>false);
		}
		parent::loadAll("rp/details",$data);
	}
}
