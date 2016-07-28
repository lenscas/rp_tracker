<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Modifiers extends RP_Parent {
	public function __construct(){
		parent::__construct();
		$this->load->model("Modiers_model");
	}
	
}
