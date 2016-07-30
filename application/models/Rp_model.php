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
		//get the id of the sheet used using the given code.
		$postData['statSheetId']=$this->db->select("id")->from("statSheets")->where("code",$postData['statSheetCode'])->get()->row()->id;
		if($postData['statSheetId']){
			unset($postData['statSheetCode']);
		}else {
			return array("success"=>false,"error"=>"The selected statsheet does not exist");
		}
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
		return	$this->db->select("rolePlays.id,
		rolePlays.name,
		rolePlays.code,
		rolePlays.isPrivate,
		rolePlays.startingStatAmount,
		rolePlays.startingAbilityAmount,
		rolePlays.description,
		rolePlays.creator,
		rolePlays.statSheetId,
		statSheets.name AS statSheetName")
				->from("rolePlays")
				->where("rolePlays.code",$rpCode)
				->join("statSheets","statSheets.id=rolePlays.statSheetId")
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
			$rp->username	=	$this->db->select("users.username")
								->from("users")
								->where("id",$rp->creator)
								->get()
								->row()
								->username;
		}
		
		return $rp;
	}
	public function getRPConfigByCode($rpCode,$userId=false){
		$rp=$this->getRPByCode($rpCode);
		if($rp){
			$config=array();
			$config['max']=	array("startingStatAmount"=>$rp->startingStatAmount,"startingAbilityAmount"=>$rp->startingAbilityAmount);
			$config['statSheet']	=	$this->db->select("statsInSheet.id,statsInSheet.name,statsInSheet.description, statRoles.description AS fallbackDescription")
										->from("statsInSheet")
										->join("statRoles","statRoles.id=statsInSheet.roleId")
										->where("statSheetId",$rp->statSheetId)
										->get()
										->result();
			if($userId){
				$id	=	$this->db->select("id")
									->from("players")
									->where("rpId",$rp->id)
									->where("is_GM",1)
									->get()
									->row();
				if($id){
					$config['isGM']=true;
				}else{
					$config['isGM']=false;
				}
			}
			return array ("success"=>true,"data"=>$config);
		}
		return array("success"=>false,"error"=>"The rp does not exist");
	}
	public function getAllStatSheets(){
		return $this->db->select("code,name,description")->from("statSheets")->get()->result_array();
	}
	public function checkIfGM($userId,$rpId){
		$result	=	$this->db->select("is_GM")
					->from("players")
					->where("users.id",$userId)
					->where("is_GM",1)
					->join("users","users.id=players.userId")
					->get()
					->row();
		if($result){
			return true;
		} else {
			return false;
		}
	}
}
