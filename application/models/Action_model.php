<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends MY_model {
	public function __construct(){
		parent::__construct();
	}
	private $actionTableFiels = "actions.id,actions.rpId,actions.name,actions.code";
	public function getAllActions($rpCode){
		return $this->db->select($this->actionTableFiels)
			->from("actions")
			->join("rolePlays","rolePlays.id=actions.rpId")
			->where("rolePlays.code",$rpCode)
			->get()
			->result() ?? array();
	}
}
