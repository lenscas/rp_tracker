<?php
class Input implements ArrayAccess{
	public $data = [];
	private $usedJSON = false;
	public function __construct(){
		$CI = &get_instance();
		if($CI->input->raw_input_stream){
			//TODO make sure to check if parse_str didn't get too much stuff to work with.
			//php is "nice" enough to just continue with truncated data if it does.
			parse_str($CI->input->raw_input_stream,$this->data);
			if( count($this->data) <=1 ){
				$data = (array)json_decode($CI->input->raw_input_stream,true);
				if(json_last_error() === JSON_ERROR_NONE){
					$this->data = $data;
					$this->usedJSON = true;
				}
			}
		}
	}
	public function offsetSet($offset, $value) {
		throw new Exception("changing the body is not allowed");
	}

	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	public function offsetUnset($offset) {
		throw new Exception("changing the body is not allowed");
	}

	public function offsetGet($offset) {
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
	//this function just uses $this->form_validation->run() to see if all the data is present and give the correct responce back to the client if it doesn't
	//if $data= false then it uses post data, just as $this->form_validation->run() would.
	public function checkAndErr(array $checkOn,array $data=null){
		$CI = &get_instance();
		$CI->load->library('form_validation');
		$CI->form_validation->reset_validation();
		if($data === null){
			$data = $this->data;
		}
		$CI->form_validation->set_data($data);
		foreach($checkOn as $key=>$value){
			$CI->form_validation->set_rules($value[0],$value[1],$value[2]);
		}
		if($CI->form_validation->run()){
			if($data===false){
				$data = $this->input->post();
			}
			return $data;
		} else {
			//seemed the request was missing some things :(
			//Guess we need to generate an error.
			$CI->load->view("missingFields");
		}
	}
}