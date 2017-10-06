<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Socket_model extends MY_model{
	private $tableName = "socketRegisterQueue";
	public function __construct(){
		parent::__construct();
	}
	public function getRegCode($userId){
		
		$code = parent::generateId($this->tableName);
		$this->db->insert(
			$this->tableName,
			[
				"code"    => $code,
				"userId"  => $userId,
				"time"    => time(),
				"notUsed" => 1
			]
		);
		return $code;
	}
	public function checkCode($code){
		$oneHour = 3600;
		$oneHourAgo = time()-$oneHour;
		$res = $this->db->select("userId,id")
			->from($this->tableName)
			->where("code",$code)
			->where("notUsed",1)
			->where("time >=",$oneHourAgo)
			->get()
			->row();
		if(!$res){
			return false;
		}
		$this->db->where("id",$res->id)->update($this->tableName,["notUsed"=>0]);
		return $res->userId;
	}
}
