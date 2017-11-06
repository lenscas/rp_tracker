<?php
class Stat_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}
	public function insertStats($data,$rpId){
		$fields = ["name","internalName","description"];
		$insertData = parent::prepareInsertData($fields,$data,["rpId"=>$rpId]);
		$this->db->insert_batch("stats",$insertData);
	}
	public function insertActions($data,$rpId){
		$fields = ["name","code"];
		$insertData = parent::prepareInsertData($fields,$data,["rpId"=>$rpId]);
		$this->db->insert_batch("actions",$insertData);
	}
	public function getStats($rpId){
		return $this->db->select("id,name,internalName,description")
			->from("stats")->where("rpId",$rpId)->get()->result() ?? array();
	}
	public function getAllDefaultStats(){
		$defaultBattleSystems = $this->db->select("id,name,internalName,description")
			->from("battleSystems")
			->get()
			->result() ?? array();
		$stats = array();
		foreach($defaultBattleSystems as $key=>$value){
			$insertArr = ["battleSystem"=>$value,"stats"=>array()];
			$insertArr["stats"] = $this->db->select("name,intName,description")
				->from("defaultStats")
				->where("battleSystemId",$value->id)
				->get()
				->result() ?? array();
			$stats[]=$insertArr;
		}
		return $stats;
	}
	public function insertStatsUsingDefaultSystem($systemId,$rpId){
		$stats = $this->db->select("name,intName as internalName,description")
			->from("defaultStats")
			->where("battleSystemId",$systemId)
			->get()
			->result_array() ?? array();
		$this->insertStats($stats,$rpId);
	}
}
