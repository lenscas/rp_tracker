<?php
class Rp_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}
	public function create($userId,$postData){
		//prepare the data
		$insertData = [
			"name"                  => $postData["name"],
			"startingStatAmount"    => $postData["startingStatAmount"],
			"startingAbilityAmount" => $postData["startingAbilityAmount"],
			"description"           => $postData["description"],
			"battleSystemId"        => $postData["battleSystem"]
		];
		$insertData['code']=parent::createCode("rolePlays");
		if(isset($postData['isPrivate']) && $postData['isPrivate']=="true"){
			$insertData['isPrivate']=1;
		} else {
			$insertData['isPrivate']=0;
		}
		$insertData['creator']=$userId;
		//insert it all!
		$this->db->trans_start();
			$this->db->insert("rolePlays",$insertData);
			$rpId=$this->db->insert_id();
			$this->load->model("Stat_model");
			if($postData["battleSystem"]==="custom"){
				$this->Stat_model->insertStats($postData["statList"],$rpId);
				$this->Stat_model->insertActions($postData["actionList"],$rpId);
			} else {
				$this->Stat_model->insertStatsUsingDefaultSystem(
					$postData["battleSystem"],$rpId
				);
			}
			
		$this->db->trans_complete();
		$this->joinRp($userId,$rpId,1);
		return array("success"=>true,"code"=>$insertData['code']);
	}
	public function checkIfJoined($userId,$rpId=false,$rpCode=false){
		$this->db->select("players.id")
		->from("players")
		->where("players.userId",$userId);
		if($rpCode){
			$this->db->where("rolePlays.code",$rpCode)
			->join("rolePlays","rolePlays.id=players.rpId");
			
		}else {
			$this->db->where("players.rpId",$rpId);
		}
		
		return $this->db->get()
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
		$data = $this->db->select("rolePlays.id,
				rolePlays.name,
				rolePlays.code,
				rolePlays.isPrivate,
				rolePlays.startingStatAmount,
				rolePlays.startingAbilityAmount,
				rolePlays.description,
				rolePlays.creator,
				battleSystems.name as systemName,
				battleSystems.internalName as intName"
			)
			->from("rolePlays")
			->join(
				"battleSystems",
				"battleSystems.id=rolePlays.battleSystemId",
				"left"
			)
			->where("rolePlays.code",$rpCode)
			->get()
			->row();
		$data->systemName = $data->systemName ?? "Custom";
		$data->intName = $data->intName ?? "CUST";
		return $data;
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
		$rpList =  $this->db->select("rolePlays.name, rolePlays.description,users.email, users.username,rolePlays.code")
				->from("rolePlays")
				->where("rolePlays.isPrivate",0)
				->join("users","users.id=rolePlays.creator")
				->get()
				->result_array();
		if($rpList){
			$this->load->model("Users_model");
			//echo "wtf!";
			$rpList = $this->Users_model->replaceEmailWithGravInList($rpList);
		}
		return $rpList;
	}
	public function getWholeRp($rpCode,$includeHidden=false){
		$rp=$this->getRPByCode($rpCode);
		if($rp){
			$this->load->model("Character_model");
			$data =$this->Character_model->getCharListByRPCode($rpCode,$includeHidden);
			$rp->characters=[];
			if($data){
				$rp->characters = $data["characters"];
			}
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
			$config['max'] = [
				"startingStatAmount"    => $rp->startingStatAmount,
				"startingAbilityAmount" => $rp->startingAbilityAmount
			];
			$config["system"] = $rp->systemName;
			$config["intName"] = $rp->intName;
			$this->load->model("Stat_model");
			$config['statSheet'] = $this->Stat_model->getStats($rp->id);
			if($userId){
				$config['isGM']=$this->checkIfGM($userId,$rp->id);
			}
			return array ("success"=>true,"data"=>$config);
		}
		return array("success"=>false,"error"=>"The rp does not exist");
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
	public function getAllRPFromUser($userId){
		return	$this->db->select("rolePlays.code,rolePlays.name,rolePlays.description")
				->from("rolePlays")
				->where("rolePlays.isPrivate",0)
				->where("rolePlays.creator",$userId)
				->get()
				->result();
	}
	public function getAllJoinedRp($userId){
		return	$this->db->select("rolePlays.code,rolePlays.name,rolePlays.description")
				->from("rolePlays")
				->join("players","players.rpId=rolePlays.id")
				->where("rolePlays.isPrivate",0)
				->where("players.userId",$userId)
				->get()
				->result();
	}
}
