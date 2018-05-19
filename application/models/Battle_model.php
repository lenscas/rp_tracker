<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Battle_model extends MY_model{
	public function __construct(){
		parent::__construct();
	}
	public function createBattle($rpId,$data){
		$chars = false;
		if($data["battle"]){
			$this->db->trans_start();
			$chars = $data["characters"];
			unset($data["characters"]);
			$data = $data["battle"];
		}
		$data["rpId"]=$rpId;
		$this->db->insert("battle",$data);
		$battleId = $this->db->insert_id();
		if($chars){
			$this->insertCharsInBattle($battleId,$rpId,$chars);
			$this->db->trans_complete();
			if($this->db->trans_status()===false){
				$battleId = null;
			}
		}
		return $battleId;
	}
	public function getAllUsersFromBattle($rpId,$battleId,$blackList=array()){
		$this->db->select("players.userId")
			->from("battle")
			->join("rolePlays","rolePlays.id=battle.rpId")
			->join("players","players.rpId=rolePlays.id")
			->where("rolePlays.id",$rpId)
			->where("battle.id",$battleId);
		if(!empty($blackList)){
			$this->db->where_not_in("players.userId",$blackList);
		}
		return $this->db->get()->result();
	}
	public function insertCharsInBattle($battleId,$rpId,$data,$useRPCode=false){
		if(empty($data)){
			return false;
		}
		$insertData=array();
		$this->load->model("Character_model");
		$turnOrder=0;
		foreach($data as $key => $value){
			$characterId = $this->Character_model->charCodeToCharId($value,$rpId,!$useRPCode);
			if(empty($characterId)){
				continue;
			}
			$insertData[$key] = [
				"battleId" => $battleId,
				"charId"   => $characterId,
				"turnOrder"=> $turnOrder++,
				"isTurn"   => $turnOrder==1
			];
		}
		if(empty($insertData)){
			return false;
		}
		$this->db->insert_batch("charsInBattle",$insertData);
		return true;
	}
	public function getAllBattleSystems(){
		return $this->db->select("id,name,internalName,description")
			->from("battleSystems")
			->get()
			->result() ?? array();
	}
	public function insertCharInBattle($battleId,$charData){
		$this->db->insert("charsInBattle",$charData);
	}
	public function updateCharacter($where,$charData){
		$this->db->where($where)->limit(1)->update("charsInBattle",$charData);
	}
	public function getAllBattles($rpId,$useRPCode){
		$this->db->select("battle.id,battle.name,battle.link")
		->from("battle");
		if($useRPCode){
			$this->db->where("rolePlays.code",$rpId);
		}else {
			$this->db->where("rolePlays.id",$rpId);
		}
		return	$this->db->join("rolePlays","rolePlays.id=battle.rpId")
				->get()
				->result_array();
	}
	public function getAllCharsInBattle($rpId,$useRPCode){
		$this->db->select("
			characters.name,
			characters.code,
			charsInBattle.battleId,
			charsInBattle.turnOrder,
			charsInBattle.isTurn
		")
		->from("charsInBattle")
		->join("characters","characters.id=charsInBattle.charId")
		->join("battle","battle.id=charsInBattle.battleId")
		->join("rolePlays","rolePlays.id=battle.rpId");
		if($useRPCode){
			$this->db->where("rolePlays.code",$rpId);
		}else {
			$this->db->where("rolePlays.id",$rpId);
		}
		$result=$this->db->group_by("charsInBattle.id")
			->get()
			->result();
		return $result;
	}
	public function getRPCodeByBattle($battleId){
		$result	=	$this->db->select("rolePlays.code")
					->from("battle")
					->join("rolePlays","rolePlays.id=battle.rpId")
					->where("battle.id",$battleId)
					->get()
					->row();
		if($result){
			return $result->code;
		}
		return null;
	}
	public function getAllCharsFromBattle($battleId){
		return $this->db->select("
			characters.name,
			characters.code,
			charsInBattle.id,
			charsInBattle.battleId,
			charsInBattle.turnOrder,
			charsInBattle.isTurn
		")
		->from("charsInBattle")
		->join("characters","characters.id=charsInBattle.charId")
		->join("battle","battle.id=charsInBattle.battleId")
		->join("rolePlays","rolePlays.id=battle.rpId")
		->where("battle.id",$battleId)
		->get()
		->result();
	}
	public function getBattle($rpCode,$battleId,$allowHidden=false){
		$data=[
			"battle"     => [],
			"modifiers"  => [],
			"tags"       => [],
			"characters" => []
		];
		$data["battle"] = $this->db->select("
				rolePlays.code,
				battle.name,
				battle.link
			")
			->from("battle")
			->join("rolePlays","rolePlays.id=battle.rpId")
			->where("battle.id",$battleId)
			->where("rolePlays.code",$rpCode)
			->limit(1)
			->get()
			->row();
		if ($data["battle"]){
			$data["characters"]=$this->getAllCharsFromBattle($battleId);
			$this->load->model("Tag_model");
			$data["tags"] = $this->Tag_model->getAllTagsByCharList($data["characters"]);
			if(!$allowHidden){
				//echo "remove all hiddens";
				$battle=$data["battle"];
				//var_dump($data);
				$data = $this->Tag_model->removeAllHiddenFromCharList($data["characters"],$data["tags"]);
				$data["battle"]=$battle;
			}
			if($data["characters"]){
				$this->load->model("Modifiers_model");
				$data["modifiers"] = $this->Modifiers_model->getAllModsFromCharList($data["characters"]);
			}
		}
		return $data;
	}
	public function getAllUsersInBattle($rpCode,$battleId){
		return $this->db->select("players.userId")
			->from("charsInBattle")
			->join("characters","characters.id=charsInBattle.charId")
			->join("players","players.id=characters.playerId")
			->where("battleId",$battleId)
			->get()
			->result();
	}
	public function checkIfBattleInRP($rpId,$battleId,$useCode = false){
		$this->db->select("battle.id")
		->from("battle")
		->where("battle.id",$battleId);
		if($useCode){
			$this->db->join("rolePlays","rolePlays.id=battle.rpId")
			->where("rolePlays.code",$rpId);
		} else {
			$this->db->where("rpId",$rpId);
		}
		return (bool)($this->db->limit(1)
		->get()
		->row()
		->id ?? false);
	}
	public function checkIfMayEndTurn($rpCode,$battleId,$userId){
		return $this->db->select("charsInBattle.id")
			->from("charsInBattle")
			->join("characters","characters.id=charsInBattle.charId")
			->join("players","players.id=characters.playerId")
			->join("rolePlays","rolePlays.id=players.rpId")
			->join("battle","battle.id=charsInBattle.battleId")
			->where("charsInBattle.isTurn",1)
			->where("battle.id",$battleId)
			->where("rolePlays.code",$rpCode)
			->where("players.userId",$userId)
			->limit(1)
			->get()
			->row()
			->id ?? false;
	}
}
