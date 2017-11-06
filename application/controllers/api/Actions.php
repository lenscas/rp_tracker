<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Actions extends API_Parent {
	public function __construct(){
		parent::__construct(true,true);
		$this->load->model("Action_model");
	}
	public function getAllActions($rpCode) {
		$actions = $this->Action_model->getAllActions($rpCode);
		parent::niceReturn($actions,["die"=>true]);
	}
}
