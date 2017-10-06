<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Socket extends API_Parent {
	public function __construct(){
		parent::__construct(false);
		$this->config->load("socket");
		$this->load->model("socket_model");
	}
	public function getConfig(){
		$data = [
			"url"          => $this->config->item("url"),
			"port"         => $this->config->item("port"),
			"registerCode" => $this->socket_model->getRegCode(parent::getIdForced())
		];
		parent::niceReturn($data);
	}
	public function checkRegisterCode($code){
		parent::forcePadServer();
		$userId = $this->socket_model->checkCode($code);
		if($userId){
			parent::niceReturn(["code"=>$userId]);
		} else {
			parent::niceReturn();
		}
	}
}
