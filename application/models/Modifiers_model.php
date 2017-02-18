<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Modifiers_model extends MY_model {
	public function __construct(){
		parent::__construct();
	}
	private $batchSelect="modifiers.name,
				modifiers.value,
				modifiers.countDown,
				modifiers.id AS modifiersId,
				statsInSheet.name AS statName,
				statsInSheet.id as statId,
				characters.code";
	public function getAllModiersByRPCode($rpCode){
		return	$this->db->select($this->batchSelect)
				->from("rolePlays")
				->join("players","players.rpId=rolePlays.id")
				->join("characters","characters.playerId=players.id")
				->join("modifiers","modifiers.charId=characters.id")
				->join("statsInSheet","statsInSheet.id=modifiers.statId")
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
		->join("statsInSheet","statsInSheet.id=modifiers.statId");
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
		return	$this->db->select("modifiers.value, statsInSheet.name")
				->from("modifiers")
				->join("statsInSheet","statsInSheet.id=modifiers.statId")
				->where("modifiers.isBase",1)
				->where("modifiers.charId",$charId)
				->get()
				->result_array();
	}
	public function updateModifier($modId,$data){
		$this->db->where("id",$modId)->update("modifiers",$data);
	}
	public function insertModifier($data,$charId=false){
		if($charId){
			$data['charId']=$charId;
		}
		$this->db->insert("modifiers",$data);
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
	
}
