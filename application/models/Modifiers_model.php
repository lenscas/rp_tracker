<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Modifiers_model extends MY_model {
	public function __construct(){
		parent::__construct();
	}
	private $batchSelect="modifiers.name,
				modifiers.value,
				modifiers.countDown,
				modifiers.isBase,
				modifiers.id AS modifiersId,
				stats.internalName as intName,
				stats.name AS statName,
				stats.id as statId,
				characters.code";
	public function getAllModiersByRPCode($rpCode){
		return	$this->db->select($this->batchSelect)
				->from("rolePlays")
				->join("players","players.rpId=rolePlays.id")
				->join("characters","characters.playerId=players.id")
				->join("modifiers","modifiers.charId=characters.id")
				->join("stats","stats.id=modifiers.statId")
				->where("rolePlays.code",$rpCode)
				->order_by("characters.id")
				->order_by("modifiers.statId")
				->get()
				->result_array();
	}
	//this is pretty much the same as getAllModiersByRPCode except it works on a list instead of getting everything for each character in an rp
	public function getAllModsFromCharList($charList){	
		$count=0;
		$this->db->select($this->batchSelect)
		->from("rolePlays")
		->join("players","players.rpId=rolePlays.id")
		->join("characters","characters.playerId=players.id")
		->join("modifiers","modifiers.charId=characters.id")
		->join("stats","stats.id=modifiers.statId");
		foreach($charList as $key=>$value){
			if(isset($value->code)){
				$this->db->or_where("characters.code",$value->code);
				$count++;
			}
			
		}
		if($count){
			return	$this->db->order_by("characters.id")
				->order_by("modifiers.statId")
				->get()
				->result_array();
		} else {
			$this->db->reset_query();//we don't want to execute it as the list was empty. Thus lets reset the query builder
		}
		
	}

	public function insert_batch($charId,$data,$isBase=false){
		if(! $data){
			return;
		}
		if($isBase){
			$insertData=array();
			foreach($data as $key=>$value){
				$insertData[]=array("charId"=>$charId,"statId"=>$key,"isBase"=>1,"name"=>"Base","value"=>$value,"countDown"=>-1);
				unset($data[$key]);
			}
			$data=$insertData;
		}
		$this->db->insert_batch("modifiers",$data);
	}
	public function getStatsFromChar($charId){
		return	$this->db->select("modifiers.value, stats.name, modifiers.id")
				->from("modifiers")
				->join("stats","stats.id=modifiers.statId")
				->where("modifiers.isBase",1)
				->where("modifiers.charId",$charId)
				->get()
				->result_array();
	}
	public function updateModifier($modId,$data){
		$this->db->where("id",$modId)->update("modifiers",$data);
	}
	private function getStatIdByIntNameAndRPID($statIntName,$rpId){
		return $this->db->select("stats.id")
			->from("stats")
			->where("internalName",$statIntName)
			->where("rpId",$rpId)
			->limit(1)
			->get()
			->row()
			->id ?? false;
	}
	public function insertModifier($data,$charId=false){
		$insertData = [
			"charId"    => $charId!==false ? $charId : $data["charId"],
			"isBase"    => $data["isBase"] ?? 0,
			"name"      => $data["name"],
			"value"     => $data["value"],
			"countDown" => $data["countDown"]
		];
		if(isset($data["intName"]) && isset($data["rpId"])){
			$insertData["statId"] = $this->getStatIdByIntNameAndRPID($data["intName"],$data["rpId"]);
		}
		$insertData["statId"] = $insertData["statId"] ?? $data["statId"] ?? false;
		if($insertData["statId"]===false){
			return false;
		}
		$this->db->insert("modifiers",$insertData);
		return	$this->db->insert_id();
	}
	public function getRPfromMod($modId){
		return	$this->db->select("rolePlays.id")
				->from("modifiers")
				->where("modifiers.id",$modId)
				->join("characters","modifiers.charId=characters.id")
				->join("players","characters.playerId=players.id")
				->join("rolePlays","rolePlays.id=players.rpId")
				->get()
				->row();
	}
	public function delete($modId,$noBase=true){
		$this->db->where("id",$modId);
		if($noBase){
			$this->db->where("isBase",0);
		}
		$this->db->delete("modifiers");
		return	$this->db->affected_rows(); 
	}
	public function getTotalStat($charId,$statRole,$isMultipleChar=false,$byCode=false){
		//decides if we need to use the where using the character code or its id.
		if($byCode){
			$collomName="characters.code";
		}else {
			$collomName="characters.id";
		}
		$this->db->select_sum("modifiers.value ", $statRole );
		$this->db->select("characters.id" )//, SUM(modifiers.value) AS ".$statRole
		//join every table that is needed so we can select on its role.
		->from("characters")
		->join("modifiers","modifiers.charId=characters.id")
		->join("statsInSheet","modifiers.statId=statsInSheet.id")
		->join("statRoles","statsInSheet.roleId=statRoles.id");
		//check if $charId contains multiple id's. If it does we loop over it else 1 where is enough
		if($isMultipleChar){
			//we put the wheres all in a group else it will break, horrible 
			$this->db->group_start();
			foreach($charId as $key=>$value){
				$this->db->or_where($collomName,$value);
			}
			$this->db->group_end();
		}else {
			$this->db->where($collomName,$value);
		}
		//now we get the query. 
		$query=	$this->db->where("statRoles.role",$statRole)
				->group_by("characters.id")
				->get();
		//if we have multiple id's we return 1 row, else we return multiple.
		if($isMultipleChar){
			return $query->result_array();
		}else {
			return $query->row_array();
		}
	}
	public function updateBaseStats($stats){
		$statsCount = count($stats); //we need this multiple times
		//first we are going to get the stats that will be updated.
		//This allows us both to check if all the stats are indeed base mods and if the user is not an GM if the amount of stats allocated stay the same
		$this->db->select("value")
		->from("modifiers")
		->limit($statsCount) //at a max we only get the count() of $stats returned. Better to not waste time searching for more
		->where("isBase",1);//only base stats are allowed to be edited this way!
		foreach($stats as $key=>$value){
			$this->db->or_where("id",$key);//the key is the id of the modifier
		}
		$res = $this->db->get()->result();
		if(!$res){
			return RP_ERROR_NOT_FOUND;
		}
		//now, its time to finally update the character's stats
		foreach($stats as $key=>$value){
			$this->db->where("id",$key)
			->limit(1)
			->set("value",$value)
			->update("modifiers");
		}
		return RP_ERROR_NONE;
	}
	public function getUserIdByMod($modId){
		return $this->db->select("players.userId")
			->from("modifiers")
			->join("characters","modifiers.charId=characters.id")
			->join("players","characters.playerId=players.id")
			->limit(1)
			->get()->row()->userId ?? false;
	}
}
