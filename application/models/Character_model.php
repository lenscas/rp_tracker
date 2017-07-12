<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Character_model extends MY_model{
	public function __construct(){
		parent::__construct();
	}
	public function getAbilitesFromCharCode($code){
		return	$this->db->select("abilities.name,abilities.cooldown,abilities.description,abilities.id")
				->from("abilities")
				->join("characters","characters.id=abilities.charId")
				->where("characters.code",$code)
				->get()
				->result_array();
	}
	public function creatCharacter($userId,$rpCode,$data){
		//load the rp model as some of its functions are needed
		$this->load->model("Rp_model");
		//first check if the code was valid
		$rp=$this->Rp_model->getRPByCode($rpCode);
		if(!$rp){
			return array("success"=>RP_ERROR_GENERIC,"error"=>"code is not a valid rp");
		}
		//Now, check if the player joined the rp
		$player=$this->Rp_model->checkInRp($userId,$rp->id);
		if(!$player){
			return array("success"=>RP_ERROR_GENERIC,"error"=>"User did not join the rp.");
		}
		//only GM's are allowed to make hidden characters
		$tags=[];
		if($player->is_GM && ! empty($data['isHidden']) && $data["isHidden"]){	
			$tags[]="hidden";
		}
		unset($data['isHidden']);
		//copy over the stats as they need to be inserted seperatly and then unset them to prevent problems
		$stats=isset($data['stats']) ? $data['stats'] : array();
		unset($data['stats']);
		//get all the abilities in a seperate array as they are needed later
		$abilities=isset($data['abilities']) ? $data['abilities'] : array();
		//if the user submitted an url as picture we need to set that up correct as well
		$hasGivenURL=false;
		if(!empty($data["appearancePictureUrl"]) && $data["appearancePictureUrl"]!=""){
			$data["isLocalImage"]=0;
			$hasGivenURL=true;
			$data["appearancePicture"]=$data["appearancePictureUrl"];
		}
		unset($data["appearancePictureUrl"]);
		//remove the abilities from the insert list as they need to be inserted seperatly
		unset($data['abilities']);
		$data['playerId']=$player->id;
		$data['code']=parent::createCode("characters");
		$this->db->insert("characters",$data);
		$data['charId']=$this->db->insert_id();
		//now, lets insert the abilities, first do the last bit of preperation to the ability array
		if($abilities){
			foreach ($abilities as $key=>$value){
				$abilities[$key]['charId']=$data['charId'];
				$abilities[$key]['name']=$abilities[$key]['name'];
			}
			$this->db->insert_batch("abilities",$abilities);
		}
		
		//now its time to insert the stats. First we load in the modifiers model
		$this->load->model("Modifiers_model");
		$this->Modifiers_model->insert_batch($data['charId'],$stats,true);
		$this->load->model("Tag_model");
		$this->Tag_model->linkCharWIthTagByRoleBatch($data["charId"],$tags);
		return array("success"=>RP_ERROR_NONE,"code"=>$data["code"]);
	}
	public function setPicture($charId,$fileName,$needChecks=true){
		if($needChecks){
		
		}
		$this->db->set("appearancePicture","assets/uploads/characters/".$fileName)
		->where("id",$charId)
		->update("characters");
		return array("success"=>true);
	}
	public function getCharacter($charCode,$simple=false,$includeHidden=false,$rpCheck=false){
		$this->db->select("	characters.id,
							characters.playerId,
							characters.name,
							characters.age,
							characters.appearancePicture,
							characters.isLocalImage,
							characters.appearanceDescription,
							characters.backstory,
							characters.personality,
							characters.code,
							characters.notes,
							players.userId"
						)
		->from("characters")
		->join("players","players.id=characters.playerId")
		->where("characters.code",$charCode)
		->limit(1);
		if($rpCheck){
			$this->db->where("rolePlays.code",$rpCheck)
			->join("rolePlays","rolePlays.id=players.rpId");
		}
		$char	=	$this->db->get()->row();
		if($char){ //if the character does not exist we don't need to run this code, at all
			$this->load->model("Tag_model");
			$char->tags = $this->Tag_model->getTagsOnCharacter($char->id); //we want all the tags regardless if its a simple get or not.
			if(!$includeHidden){
				$isHidden=$this->Tag_model->checkForTagRole(false,"Hidden",$char->tags);//check if there is a hidden tag. We don't need to make another query as we already have all the tags
				if($isHidden){
					$char=null;	//we want to make sure the rest of the program behaves EXACTLY the same as when a character does not exist.
								//The easiest way to do it is by setting it null, as that is the same value as when it does not exist
				}
			}
		}
		if($simple){
			return $char;
		}
		if($char){
			$this->load->model("Modifiers_model");
			$char->stats=$this->Modifiers_model->getStatsFromChar($char->id);
			unset($char->id);
			return array("success"=>true,"character"=>$char);
		} else {
			return array("success"=>false,"error"=>"This character does not exist");
		}
	
	}
	public function getCharListByRPCode($rpCode,$includeHidden=false){
		$data=array();
		$data['characters']=$this->db->select("	characters.playerId,
												characters.name,
												characters.age,
												characters.appearancePicture,
												characters.appearanceDescription,
												characters.isLocalImage,
												characters.backstory,
												characters.personality,
												characters.code,
												characters.notes")
							->from("rolePlays")
							->join("players","rolePlays.id=players.rpId")
							->join("characters","characters.playerId=players.id")
							->where("rolePlays.code",$rpCode)
							->get()
							->result();
		if($data["characters"]){
			$this->load->model("Tag_model");
			$data["tags"]=$this->Tag_model->getAllTagsInRP(false,$rpCode);
			if(!$includeHidden){
				$data = $this->Tag_model->removeAllHiddenFromCharList($data["characters"],$data["tags"]);
			}
			$this->load->model("Modifiers_model");
			if($includeHidden){
				$data['modifiers']=$this->Modifiers_model->getAllModiersByRPCode($rpCode); //this is faster then looping over all the characters and getting the modifiers that way
			} else {
				$data["modifiers"]=$this->Modifiers_model->getAllModsFromCharList($data["characters"]);
			}
		}
		return $data;
	}
	public function getAbilitiesByCharInRP($rpCode){
		return	$this->db->select("characters.name,abilities.id,abilities.name as abilityName, abilities.cooldown,abilities.countDown")
				->from("rolePlays")
				->join("players","players.RPId=rolePlays.id")
				->join("characters","characters.playerId=players.id")
				->join("abilities","abilities.charId=characters.id")
				->where("rolePlays.code",$rpCode)
				->order_by("characters.name")
				->get()
				->result();
	}
	public function getRPIdByChar($charCode=false,$charId=false){
		$this->db->select("rolePlays.id")
		->from("characters");
		if($charCode){
			$this->db->where("characters.code",$charCode);
		} else {
			$this->db->where("characters.id",$charId);
		}
		return	$this->db->join("players","players.id=characters.playerId")
				->join("rolePlays","rolePlays.id=players.rpId")
				->get()
				->row();
	}
	public function getRPCodeByChar($charCode=false,$charId=false){
		$this->db->select("rolePlays.code")
		->from("characters");
		if($charCode){
			$this->db->where("characters.code",$charCode);
		} else {
			$this->db->where("characters.id",$charId);
		}
		$data	=	$this->db->join("players","players.id=characters.playerId")
					->join("rolePlays","rolePlays.id=players.rpId")
					->get()
					->row();
		if($data){
			return $data->code;
		}
	}
	public function getAbilitiesByCharList($charList){
		if(empty($charList)){
			return;
		}
		$this	->db->select("characters.name,abilities.id,abilities.name as abilityName, abilities.cooldown,abilities.countDown")
				->from("rolePlays")
				->join("players","players.RPId=rolePlays.id")
				->join("characters","characters.playerId=players.id")
				->join("abilities","abilities.charId=characters.id");
		//though the list is not empty it may be the case that all characters inside the list are hidden
		//to get arround this we keep track of how many characters are not hidden. If there are 0 we reset the db class and return null
		$count=0;
		foreach($charList as $key=>$value){
			if(isset($value->code)){
				//echo "wtf?";
				$this->db->where("characters.code",$value->code);
				$count++;
			}
			
		}
		if($count<=0){
			$this->db->reset_query();
			return;
		}
		//->where("rolePlays.code",$rpCode)
		return	$this->db->order_by("characters.name")
				->get()
				->result();
	}
	//the $isGM is passed as reference and will be filled with 
	public function checkIfUserMayEdit($rpCode,$charCode,$userId,&$isGM=null){
		$mayEdit = false;
		//get the character
		$res =	$this->db->select("players.userId")
				->from("characters")
				->join("players","players.id=characters.playerId")
				->where("characters.code",$charCode)
				->limit(1)
				->get()
				->row();
		if(!$res){
			//character does not exist. BURN THE HOUSE DOWN!
			throw new Exception("Character does not exist");
		} elseif($res->userId==$userId){
			//the user is the creator of this character thus he is allowed to edit it
			$mayEdit=true;
		}
		//no else here as $mayEdit defaults to false anyway
		//look if the user is an GM
		$this->load->model("Rp_model");
		$isGM = $this->Rp_model->checkIfGM($this->userId,$rpCode);
		//return true if either he is an GM or if $mayEdit is true. 
		return $isGM || $mayEdit;
	}
	public function updateCharacter($charId,$data,$useCode=false){
		if($useCode){
			$this->db->where("code",$charId);
		} else {
			$this->db->where("id",$charId);
		}
		$this->db->limit(1)->update("characters",$data);
	}
	public function updateAbilities($charId,$data,$isGM=true,$useCode=false){
		if($useCode){
			$res = $this->db->select("id")->from("characters")->limit(1)->where("code",$charId)->get()->row();
			if(!$res){
				return RP_ERROR_NOT_FOUND;
			}
			$charId = $res->id;
		}
		foreach($data as $key=>$value){
			$this->db->where("id",$key)
			->limit(1)
			->where("charId",$charId);
			if(isset($value["cooldown"])){
				$this->db->set("cooldown",$value["cooldown"]);
			}
			if(isset($value["description"])){
				$this->db->set("description",$value["description"]);
			}
			if(isset($value["name"])){
				$this->db->set("name",$value["name"]);
			}
			$this->db->update("abilities");
		}
		return RP_ERROR_NONE;
		
	}
	
}


?>
