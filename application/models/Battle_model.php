<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Battle_model extends MY_model{
	public function __construct(){
		parent::__construct();
	}
	public function createBattle($data){
		$this->db->insert("battle",$data);
		return $this->db->insert_id();
	}
	public function insertCharsInBattle($data){
		$this->db->insert_batch("charsInBattle",$data);
	}
	//$rpId this automatically puts the rpId in the new array. Usefull when preparing to insert it, not so usefull otherwise.
	//$makeIsTurnValue this automatically makes a value that shows who's turn it is. Usefull when inserting, maybe not so much otherwise
	public function decideOrder($charList,$makeIsTurnValue=false,$battleId=false){
		$newList=array();
		//generate all the rolls
		foreach($charList as $key=>$value){
			$charList[$key]['totalRoll']=0;
			for($rolls=0;$rolls<$value['evade_defense'];$rolls++){
				$charList[$key]['totalRoll']=$charList[$key]['totalRoll']+mt_rand(1,10);
			}
		}
		//set the counters correctly
		$counter=1;
		$isTurn=1;
		//make it loop for as long as there are characters in the list
		$remember=array();
		while (count($charList)){
			//this is used if an higher value is found
			$highest=0;
			//loop over all the chars and compare
			foreach($charList as $key=>$value){
				//it found a value that is higher then the previos highest value. Clear the remember array and update $highest
				if($highest<$value['totalRoll']){
					$remember=array($key);
					$highest=$value['totalRoll'];
					//it found something with the same value. Update $remember
				}elseif($highest==$value['totalRoll']){
					$remember[]=$key;
				}
			}
			//it found multiple highest values. Shuffle them
			if(count($remember)!=1){
				shuffle($remember);
			}
			//insert them all
			foreach($remember as $key=>$value){
				$newData=array("charId"=>$charList[$value]['id'],"turnOrder"=>$counter);
				if($battleId){
					$newData['battleId']=$battleId;
				}
				if($makeIsTurnValue){
					$newData['isTurn']=$isTurn;
				}
				$newList[]=$newData;
				$counter=$counter+1;
				unset($charList[$value]);
				$isTurn=0;
			}
		}
		//return the new array
		return $newList;
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
		$this->db->select("characters.name, characters.code,charsInBattle.id, charsInBattle.battleId,charsInBattle.turnOrder,charsInBattle.isTurn")
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
				->result_array();
		/*
		echo $this->db->last_query();
		die;//*/
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
		return $this->db->select("characters.name, characters.code,charsInBattle.id, charsInBattle.battleId,charsInBattle.turnOrder,charsInBattle.isTurn")
		->from("charsInBattle")
		->join("characters","characters.id=charsInBattle.charId")
		->join("battle","battle.id=charsInBattle.battleId")
		->join("rolePlays","rolePlays.id=battle.rpId")
		->where("battle.id",$battleId)
		->get()
		->result();
	}
	public function getBattle($battleId){
		$battle =	$this->db->select("rolePlays.code,battle.name,battle.link")
					->from("battle")
					->join("rolePlays","rolePlays.id=battle.rpId")
					->where("battle.id",$battleId)
					->limit(1)
					->get()
					->row();
		if ($battle){
			$characters=$this->getAllCharsFromBattle($battleId);
			if($characters){
				$this->load->model("Modifiers_model");
				$modifiers = $this->Modifiers_model->getAllModiersByRPCode($battle->code);
				if($modifiers){
					return ["battle"=>$battle,"characters"=>$characters,"modifiers"=>$modifiers];
				}
			}
			
		}
		return false;
	}
}