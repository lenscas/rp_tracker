<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Battle_model extends MY_model{
	public function __construct(){
		parent::__construct();
	}
	public function createBattle($data){
		$this->db->insert("battle",$data);
		return $this->db->insert_id();
	}
	public function insertCharsInBattle($data){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		$this->db->insert_batch("charsInBattle",$data);
	}
	//$rpId this automatically puts the rpId in the new array. Usefull when preparing to insert it, not so usefull otherwise.
	//$makeIsTurnValue this automatically makes a value that shows who's turn it is. Usefull when inserting, maybe not so much otherwise
	public function decideOrder($charList,$makeIsTurnValue=false,$battleId=false){
		echo "<pre>";
		print_r($charList);
		echo "</pre>";
		$newList=array();
		//generate all the rolls
		foreach($charList as $key=>$value){
			$charList[$key]['totalRoll']=0;
			for($rolls=0;$rolls<$value['evade_defense'];$rolls++){
				$charList[$key]['totalRoll']=$charList[$key]['totalRoll']+mt_rand(1,10);
			}
		}
		//set the counters correctly
		$counter=1;
		$isTurn=1;
		//make it loop for as long as there are characters in the list
		$remember=array();
		while (count($charList)){
			//this is used if an higher value is found
			$highest=0;
			//loop over all the chars and compare
			foreach($charList as $key=>$value){
				//it found a value that is higher then the previos highest value. Clear the remember array and update $highest
				if($highest<$value['totalRoll']){
					$remember=array($key);
					$highest=$value['totalRoll'];
					//it found something with the same value. Update $remember
				}elseif($highest==$value['totalRoll']){
					$remember[]=$key;
				}
			}
			//it found multiple highest values. Shuffle them
			if(count($remember)!=1){
				shuffle($remember);
			}
			//insert them all
			foreach($remember as $key=>$value){
				$newData=array("charId"=>$charList[$value]['id'],"turnOrder"=>$counter);
				if($battleId){
					$newData['battleId']=$battleId;
				}
				if($makeIsTurnValue){
					$newData['isTurn']=$isTurn;
				}
				$newList[]=$newData;
				$counter=$counter+1;
				unset($charList[$value]);
				$isTurn=0;
			}
		}
		//return the new array
		return $newList;
	}
}
