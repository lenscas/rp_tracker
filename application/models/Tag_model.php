<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tag_model extends MY_model{
	public function linkCharWithTagByRole($charId,$tagRole){
		$tag = $this->getTagByRole($tagRole);
		if($tag){
			$this->db->insert("tagsOnCharacters",["characterId"=>$charId,"tagId"=>$tag->id]);
			return true;
		}
	}
	//the same as $this->linkCharWithTagByRole but does a whole batch at once
	public function linkCharWIthTagByRoleBatch($charId,$tagRoles){
		$data =[];
		foreach($tagRoles as $key=>$value){
			$tag=$this->getTagByRole($value);
			if($tag){
				$data[]=["characterId"=>$charId,"tagId"=>$tag->id];
			}
		}
		//var_dump($data);
		if(count($data)>0){
			$this->db->insert_batch("tagsOnCharacters",$data);
			return true;
		}
		return false;
	}
	public function getTagByRole($tagRole){
		return	$this->db->select("id,name,class,specialRoles")
				->from("tags")
				->where("specialRoles",$tagRole)
				->limit(1)
				->get()
				->row();
	}
	public function getTagsOnCharacter($charId=false,$charCode=false){
		$this->db->select("tags.name,tags.class,tags.specialRoles")
		->from("tags")
		->join("tagsOnCharacters","tagsOnCharacters.tagId=tags.id");
		if($charId){
			$this->db->where("tagsOnCharacters.characterId",$charId);
		}elseif($charCode){
		
		} else {
			//error code here
		}
		return $this->db->get()->result();
	}
	public function checkForTagRole($charId=false,$checkFor,$tagList=false){
		if($charId){
			$tagList=$this->getTagsOnCharacter(false,$charId);
		}
		if($tagList){
			foreach($tagList as $key=>$value){
				if($value->specialRoles==$checkFor){
					return true;
				}
			}
		}
		return false;
	}
	public function getAllTagsInRP($rpId=false,$rpCode=false){
		$this->db->select("tags.name,tags.class,tags.specialRoles,characters.code")
		->from("tags")
		->join("tagsOnCharacters","tagsOnCharacters.tagId=tags.id")
		->join("characters","characters.id=tagsOnCharacters.characterId");
		if($rpId){
		
		} elseif($rpCode){
			$this->db->join("players","players.id=characters.playerId")
			->join("rolePlays","rolePlays.id=players.rpId")
			->where("rolePlays.code",$rpCode);
		} else {
			return null;
		}
		return $this->db->get()->result();
		
	}
	public function getAllTagsByCharList($charList){
		$this->db->select("tags.name,tags.class,tags.specialRoles,characters.code")
		->from("tags")
		->join("tagsOnCharacters","tagsOnCharacters.tagId=tags.id")
		->join("characters","characters.id=tagsOnCharacters.characterId");
		foreach($charList as $key=>$value){
			$this->db->or_where("characters.code",$value->code);
		}
		return $this->db->get()->result();
	}
	public function removeAllHiddenFromCharList($charList,$tagList){
		foreach($charList as $key=>$value){
			foreach($tagList as $tagKey=>$tagValue){
				if($value->code==$tagValue->code && $tagValue->specialRoles=="Hidden"){
					//$charName=$value->name;
					unset($charList[$key]->code);
					//$charList[$key]->name=$charName;
					break;
				}
			}
		}
		return ["characters"=>array_slice($charList,0),"tags"=>array_slice($tagList,0)];
	}
}
	
