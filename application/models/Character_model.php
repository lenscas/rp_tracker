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


}


?>
