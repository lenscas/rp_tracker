<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stats extends API_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Stat_model");
	}
	public function getAllDefaultSystems(){
		$systems = $this->Stat_model->getAllDefaultStats();
		parent::niceReturn($systems,["die"=>true]);
	}
}
