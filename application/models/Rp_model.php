<?php
class Rp_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}
	public function create($userId,$postData){
		$postData['code']=parent::createCode("rolePlays");
		if($postData['isPrivate']=="true"){
			$postData['isPrivate']=1;
		} else {
			$postData['isPrivate']=0;
		}
		$postData['creator']=$userId;
		$this->db->insert("rolePlays",$postData);
		$rpId=$this->db->insert_id();
		$playerId=$this->joinRp($userId,$rpId,1);
		return array("success"=>true,"playerId"=>$playerId);
	}
	public function checkIfJoined($userId,$rpId){
		return	$this->db->select("id")
				->from("players")
				->where("players.userId",$userId)
				->where("players.rpId",$rpId)
				->get()
				->row();
	}
	public function joinRp($userId,$rpId,$isGm=0){
		$result=$this->checkIfJoined($userId,$rpId);
		if(! $result){
			$this->db->set("userId",$userId)
			->set("rpId",$rpId)
			->set("is_GM",$isGm)
			->insert('players');
			return $this->db->insert_id();	
		}
		return false;
		
	}
	public function getRPByCode($rpCode){
		return	$this->db->select("*")
				->from("rolePlays")
				->where("code",$rpCode)
				->get()
				->row();
	}
	public function checkInRp($userId,$rpId){
		return	$this->db->select("*")
				->from("players")
				->where("userId",$userId)
				->where("rpId",$rpId)
				->get()
				->row();
	}
	public function creatCharacter($userId,$rpCode,$data,$minion=0){
		//first check if the code was valid
		$rp=$this->getRPByCode($rpCode);
		if(!$rp){
			return array("success"=>false,"error"=>"code is not a valid rp");
		}
		//Now, check if the player joined the rp
		$player=$this->checkInRp($userId,$rp->id);
		if(!$player){
			return array("success"=>false,"error"=>"User did not join the rp.");
		}
		//then now, check if the player is a gm, if he is then we are going to skip the check to see if he has indeed the max amount of start stats
		if(! $player->is_GM){
			//Lets count them all up 
			$amount=$data['health']+$data['armour']+$data['strength']+$data['accuracy']+$data['magicalDefence']+$data['magicalSkill']+$data['agility'];
			if($amount!=$rp->startingStatAmount){
				return array("success"=>false,"error"=>"User did not set a correct amount of stats.");
			}
		}
		$data['playerId']=$player->id;
		$data['code']=parent::createCode("characters");
		$data['isMinion']=$minion;
		$this->db->insert("characters",$data);
		$data['charId']=$this->db->insert_id();
		return array("success"=>true,"data"=>$data);
	}
	public function setPicture($charId,$fileName,$needChecks=true){
		if($needChecks){
		
		}
		$this->db->set("appearancePicture","assets/uploads/characters/".$fileName)
		->where("id",$charId)
		->update("characters");
		return array("success"=>true);
	}
	public function getAllRPs(){
		return $this->db->select("rolePlays.name, rolePlays.description, users.username,rolePlays.code")
				->from("rolePlays")
				->where("rolePlays.isPrivate",0)
				->join("users","users.id=rolePlays.creator")
				->get()
				->result_array();
	}
	public function getWholeRp($rpCode){
		$rp=$this->getRPByCode($rpCode);
		if($rp){
			$rp->characters	=	$this->db->select("characters.name,characters.code")
								->from("characters")
								->join("players","players.id=characters.playerId")
								->where("players.rpId",$rp->id)
								->where("isMinion",0)
								->get()
								->result_array();
			$rp->username		=	$this->db->select("users.username")
								->from("users")
								->where("id",$rp->creator)
								->get()
								->row()
								->username;
		}
		
		return $rp;
	}
	public function getCharacter($charCode){
		$char	=	$this->db->select("*")
					->from("characters")
					->where("code",$charCode)
					->get()
					->row_array();
		if($char){
			unset($char['id']);
			return array("success"=>true,"character"=>$char);
		} else {
			return array("success"=>false,"error"=>"This character does not exist");
		}
	}
}
