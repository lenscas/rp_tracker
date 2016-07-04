<?php
class Character_model extends MY_model{
	public function __construct(){
		parent::__construct();
	}
	public function getAbilitesFromCharCode($code){
		return	$this->db->select("abilities.name,abilities.cooldown,abilities.description")
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
			return array("success"=>false,"error"=>"code is not a valid rp");
		}
		//Now, check if the player joined the rp
		$player=$this->Rp_model->checkInRp($userId,$rp->id);
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
		//check if the checkbox for isMinion was checked and make the char a minion if it is
		if(isset($data['isMinion'])){
			if($data['isMinion']){
				$data['isMinion']=1;
			} else {
				$data['isMinion']=0;
			}
		}else {
			$data['isMinion']=0;
		}
		//get all the abilities in a seperate array as they are needed later
		$abilities=$data['abilities'];
		
		//remove the abilities from the insert list as they need to be inserted seperatly
		unset($data['abilities']);
		$data['playerId']=$player->id;
		$data['code']=parent::createCode("characters");
		
		//load in the BB helper
		$this->load->helper("BB_helper");
		$data['notes']==parse_bbcode($data['notes']);
		$data['appearanceDescription']=parse_bbcode($data['appearanceDescription']);
		$data['backstory']=parse_bbcode($data['backstory']);
		$data['personality']=parse_bbcode($data['personality']);
		$this->db->insert("characters",$data);
		$data['charId']=$this->db->insert_id();
		//now, lets insert the abilities, first do the last bit of preperation to the ability array
		
		foreach ($abilities as $key=>$value){
			$abilities[$key]['charId']=$data['charId'];
			$abilities[$key]['name']=parse_bbcode($abilities[$key]['name']);
		}
		$this->db->insert_batch("abilities",$abilities);
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
	public function getCharacter($charCode){
		$char	=	$this->db->select("	characters.playerId,
										characters.name,
										characters.age,
										characters.appearancePicture,
										characters.appearanceDescription,
										characters.backstory,
										characters.health,
										characters.armour,
										characters.strength,
										characters.accuracy,
										characters.magicalSkill,
										characters.magicalDefence,
										characters.personality,
										characters.code,
										characters.isMinion,
										characters.agility,
										characters.notes,
										"
									)
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


?>
