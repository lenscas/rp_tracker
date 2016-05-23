<?php
class Rp_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}
	public function create($userId,$postData){
		$this->load->helper('string');
		$found=true;
		while($found){
			$code=random_string('alnum', 7);
			$found=$this->codeToId($code);
		}
		$postData['code']=$code;
		if($postData['isPrivate']=="true"){
			$postData['isPrivate']=1;
		} else {
			$postData['isPrivate']=0;
		}
		$this->db->insert("rolePlays",$postData);
		$rpId=$this->db->insert_id();
		$playerId=$this->joinRp($userId,$rpId,1);
		return array("success"=>true,"playerId"=>$playerId);
	}
	public function joinRp($userId,$rpId,$isGm=0){
		$this->db->set("userId",$userId)
		->set("rpId",$rpId)
		->set("is_GM",$isGm)
		->insert('players');
		return $this->db->insert_id();
	}
	public function getRPByCode($rpCode){
		return	$this->db->select("*")
				->from("rolePlays")
				->where("code",$rpCode)
				->get()
				->row();
	}
	public function checkInRp($userId,$rpCode){
		return	$this->db->select("*")
				->from("players")
				->where("userId",$userId)
				->where("rpId",$rpId)
				->get()
				->row();
	}
	public function creatCharacter($userId,$rpId,$data){
		//first check if the code was valid
		$rp=$this->codeToId($rpCode);
		if(!$rpId){
			return array("success"=>false,"error"=>"code is not a valid rp");
		}
		//Now, check if the player joined the rp
		$player=$this->checkInRp($userId,$rpId);
		if(!$playerId){
			return array("success"=>false,"error"=>"User did not join the rp.");
		}
		//then now, check if the player is a gm, if he is then we are going to skip the check to see if he has indeed the max amount of start stats
		if(! $player['is_GM']){
			//Lets count them all up 
			$amount=$data['health']+$data['armour']+data['strenght']+data['accuracy']+data['magicalDefence']+data['magicalSkill'];
			if($amount!=$rp['startingStatAmount']){
				return array("success"=>false,"error"=>"User did not set a correct amount of stats.");
			}
		}
		$data['playerId']=$player['id'];
		$this->db->insert("characters",$data);
		return array("success"=>true,"charId"=>$this->db->insert_id());
	}
	public function setPicture($charId,$fileName,$needChecks=true){
		if($needChecks){
		
		}
		$this->db->set("appearancePicture","assets/uploads/character/".$fileName)
		->where("id",$charId)
		->update("characters");
		return array("success"=>true);
	}
}
