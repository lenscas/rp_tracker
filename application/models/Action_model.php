<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Action_model extends MY_model {
	public function __construct(){
		parent::__construct();
	}
	private $actionTableFielsNoRPId = "actions.id,actions.name,actions.code";
	private $actionTableFiels ="actions.id,actions.name,actions.code,actions.rpId";
	public function getAllActions($rpCode,$showId=true){
		$toGet = $this->actionTableFiels;
		if(!$showId){
			$toGet = $this->actionTableFielsNoRPId;
		}
		return $this->db->select($toGet)
			->from("actions")
			->join("rolePlays","rolePlays.id=actions.rpId")
			->where("rolePlays.code",$rpCode)
			->get()
			->result() ?? array();
	}
	public function saveDeltas($deltas,$battleId,$rpId){
		$this->load->model("Lua_model");
		$error = false;
		$tempCodesToRealIds = array();
		$inActiveDeltaCodes = array();
		$message = null;
		$this->load->model("Modifiers_model");
		$this->load->model("Character_model");
		//$this->db->trans_begin();
		foreach($deltas as $key=>$delta){
			//first, make sure the delta is even useful for us.
			if($delta["what"] == $this->Lua_model->kinds["OUTPUT"]){
				continue;
			}
			if($delta["what"] == $this->Lua_model->kinds["ERROR"]){
				continue;
			}
			if($delta["mode"] == $this->Lua_model->modes["NOTHING"]){
				continue;
			}
			if($delta["isInActive"] ?? false){
				$inActiveDeltaCodes[$delta["code"]] = true;
				continue;
			}
			if($tempCodesToRealIds[$delta["code"]] ?? false){
				$error = true;
				$message = "Delta codes not unique.";
				break;
			}
			switch ($delta["mode"]){
				case $this->Lua_model->modes["INSERT"]:
					if($delta["what"] == $this->Lua_model->kinds["MODIFIER"]){
						if( empty($delta["name"])){
							$message = "Missing name for modifier";
						}
						if(empty($delta["amount"])){
							$message = "Missing value for modifier";
						}
						if(empty($delta["countDown"])){
							$message = "Missing countDown for modifier";
						}
						if(empty($delta["type"])){
							$message = "Missing type for modifier";
						}
						if($message){
							$error = true;
							break;
						}
						$charId = $tempCodesToRealIds[$delta["character"]] ?? false;
						if(!$charId){
							$charId = $this->Character_model->charCodeToCharId($delta["character"],$rpId,true);
						}
						if(!$charId){
							$error = true;
							$message = "Invalid character code.";
							break;
						}
						$modData = [
							"charId"    => $charId,
							"isBase"    => 0,
							"name"      => $delta["name"] ,
							"value"     => $delta["amount"],
							"countDown" => $delta["countDown"],
							"intName"   => $delta["type"],
							"rpId"      => $rpId
						];
						$modId = $this->Modifiers_model->insertModifier($modData);
						if(!$modId){
							$error = true;
							$message = "Problem with modifier";
							break;
						}
						$tempCodesToRealIds[$delta["code"]] = $modId;
					}
				break;
				case $this->Lua_model->modes["UPDATE"]:
					if($delta["what"]==$this->Lua_model->kinds["MODIFIER"]){
						if($inActiveDeltaCodes[$delta["modId"]] ?? false ){
							break;
						}
						$modId = $tempCodesToRealIds[$delta["modId"]] ?? false;
						if(!$modId){
							if($this->Modifiers_model->checkModifierId($delta["modId"],$rpId)){
								$modId = $delta["modId"];
							} else {
								$error   = true;
								$message = "Invalid modifier id to update.";
								break;
							}
						}
						$modData = [
							"value" =>$delta["amount"] ?? null,
							"countDown" =>$delta["countDown"] ?? null
						];
						foreach($modData as $key=>$value){
							if($value===null){
								unset($modData[$key]);
							}
						}
						$this->Modifiers_model->updateModifier($modId,$modData);
					}
				break;
				case $this->Lua_model->modes["DELETE"]:
					if($delta["what"] == $this->Lua_model->kinds["MODIFIER"]){
						if(!$this->Modifiers_model->checkModifierId($delta["modId"],$rpId)){
							$error = true;
							$message = "Invalid modifier id";
							break;
						}
						$this->Modifiers_model->delete($delta["modId"]);
					}
				break;
			}
			if($error){
				break;
			}
			
		}
		//$this-db->trans_
		return ["error"=>$error,"message"=>$message];
	}
	public function getAllDefaultActionsBySystems($battleSystems){
		$actions = array();
		foreach($battleSystems as $key=>$value){
			$battleSystem = $value;
			$stats = array();
			if( ((array)$value["battleSystem"]) ?? false){
				$battleSystem = $value["battleSystem"];
				$stats = $value["stats"];
			}
			$insertArr = [
				"battleSystem" => $battleSystem,
				"actions"      => array(),
				"stats"        => $stats
			];
			$insertArr["actions"] = $this->db->select("name,code,description")
				->from("defaultActions")
				->where("battleSystemId",$battleSystem->id)
				->get()
				->result() ?? array();
			$actions[]=$insertArr;
		}
		return $actions;
	}
	public function getAllDefaultActions($battleSystemId){
		return $this->db->select("name,code,description")
			->from("defaultActions")
			->where("battleSystemId",$battleSystemId)
			->get()
			->result();
	}
	public function copyDefaultActionsToRP($battleSystemId,$rpId){
		$actions = $this->getAllDefaultActions($battleSystemId);
		if(!$actions){
			var_dump($actions);
			return false;
		}
		$insertData = $this->prepareInsertData(
			["name","code","description"],
			$actions,
			["rpId"=>$rpId]
		);
		$this->db->insert_batch("actions",$insertData);
		return true;
	}
	public function getActionName($rpId,$actionId){
		return $this->db->select("name")
			->from("actions")
			->where("id",$actionId)
			->where("rpId",$rpId)
			->limit(1)
			->get()
			->row()
			->name ?? null;
	}
	public function insertAction($rpId,$data){
		$data["rpId"] = $rpId;
		$this->db->insert("actions",$data);
		return $this->db->insert_id();
	}
	public function updateAction($actionId,$data){
		$this->db->where("id",$actionId)->limit(1)->update("actions",$data);
	}
	public function removeAction($actionId){
		$this->db->where("id",$actionId)->limit(1)->delete("actions");
	}
	
	public function runAction($rpCode,$battleId,$config){
		$this->load->model("Rp_model");
		$rpId = $this->Rp_model->rpCodeToId($rpCode);
		$config["action"] = $this->getActionName($rpId,$config["actionId"]);
		if(!$config["action"]){
			return [
				"success" => false,
				"error"   => "Action does not exist",
				"status"  => RP_ERROR_NOT_FOUND
			];
		}
		$this->load->model("Lua_model");
		$actions = $this->getAllActions($rpCode);
		$result = $this->Lua_model->runAction($rpCode,$battleId,$config,$actions);
		if(!$result["success"]){
			return $result;
		}
		if($config["autoUpdate"] ?? false){
			$success = $this->saveEnv($result["data"],$battleId,$rpId);
			$success["output"] = $result["output"];
			return $success;
		}
		return $result;
		
		
	}
}
