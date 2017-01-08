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
}
	
