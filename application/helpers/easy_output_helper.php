<?php

function escapeData($data){
	//check if we are dealing with an array or an object
	$dataType=gettype($data);
	if($dataType=="array" || $dataType=="object"){
		//we are dealing with an object/array. To make it save to json_encode we need to escape all its values
		//start by looping over it
		foreach($data as $key=>$value){
			//get the escaped value
			$newData = escapeData($value);
			//because php's syntax is diffrent for setting values in objects and arrays we need to check which of the two we are dealing with
			if($dataType=="object"){
				//could have written it as $data->$key but I find that the {} at least help somewhat at showing what exactly goes on here
				$data->{$key} = $newData;
			} else {
				$data[$key]   = $newData;
			}
		}
	} else {
		//it is something that we can escape directly, lets do it
		$data = html_escape($data);
	}
	//whatever the data was that we where given, it is clean now. Lets return it.
	return $data;
}
function outputPlusFilter($data){
	$CI =& get_instance();
	//cast it to an array as sometimes we get an object instead of an array
	//it doesn't matter anyway for the rest of the functions
	$data = (array)$data;
	if(!isset($data["userId"]) && $CI->session->userId){
		$data["userId"] = $CI->session->userId;
	}
	$data = escapeData($data);
	$CI->output->set_output(json_encode($data));
	return $CI->output;
}
