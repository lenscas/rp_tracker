<?php
class Rp_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}
	public function create($userId,$postData){
		$this->load->helper("BB_helper");
		$postData['code']=parent::createCode("rolePlays");
		if($postData['isPrivate']=="true"){
			$postData['isPrivate']=1;
		} else {
			$postData['isPrivate']=0;
		}
		$postData['creator']=$userId;
		$postData['description']=parse_bbcode($postData['description']);
		$this->db->insert("rolePlays",$postData);
		$rpId=$this->db->insert_id();
		$this->joinRp($userId,$rpId,1);
		return array("success"=>true,"code"=>$postData['code']);
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
	public function getRPRulesByCode($rpCode){
		return	$this->db->select("startingStatAmount,startingAbilityAmount")
				->from("rolePlays")
				->where("code",$rpCode)
				->get()
				->row_array();
	}

}
