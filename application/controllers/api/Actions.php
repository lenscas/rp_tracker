<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Actions extends API_Parent {
	public function __construct(){
		parent::__construct(true,true);
		$this->load->model("Action_model");
	}
	public function getAllActions($rpCode) {
		$actions = $this->Action_model->getAllActions($rpCode,false);
		parent::niceReturn($actions,["die"=>true]);
	}
	public function runAction($rpCode,$battleId,$actionId){
		$config = parent::checkAndErr([
			["user","user","required"],
			["target","target","required"],
			["autoUpdate","autoUpdate","required"]
		]);
		$config["actionId" ]=$actionId;
		$this->load->model("Rp_model");
		$isGM = $this->Rp_model->checkIfGM($this->userId,$rpCode,true);
		if(!$isGM){
			$config["autoUpdate"] = false;
		} else {
			$config["autoUpdate"] = ($config["autoUpdate"]==="true");
		}
		$result = $this->Action_model->runAction(
			$rpCode,
			$battleId,
			$config
		);
		$this->load->model("Lua_model");
		$result["data"]["modes"] = $this->Lua_model->modes;
		$result["data"]["kinds"] = $this->Lua_model->kinds;
		
		parent::niceReturn($result);
	}

}
