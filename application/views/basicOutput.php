<?php
$CI =& get_instance();
function escapeData($data){
	//check if we are dealing with an array or an object
	$dataType=gettype($data);
	if($dataType=="array" || $dataType=="object"){
		//we are dealing with an object/array. To make it save to json_encode we need to escape all its values
		//start by looping over it
		foreach($data as $key=>$value){
			//get the escaped value
			$newData = $this->escapeData($value);
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
	//cast it to an array as sometimes we get an object instead of an array
	//it doesn't matter anyway for the rest of the functions
	$data = (array)$data;
	if(!isset($data["userId"]) && $CI->session->userId){
		$data["userId"] = $this->session->userId;
	}
	$data = escapeData($data);
	$CI->output->set_output(json_encode($data));
	return $CI->output;
}
switch($errored){
	case RP_ERROR_DUPLICATE:
		$CI->output->set_status_header(409);
		$body = [
			"messages" => [
				"One or more given values are already in use.",
				"[NAME] is already in use",
				"The given [VALUE] is already in use"
			],
			"name"  => $resourceName,
			"VALUE" => $resourceKind,
			"pref"  => $pref
		];
		break;
	case RP_ERROR_NO_PERMISSION:
		$CI->output->set_status_header(403);
		$body = [
			"messages" => [
				"Can't work on resource.",
				"No permission to process request.",
				"No permission to change resource",
			],
			"pref" => $pref
		];
	break;
	case RP_ERROR_GENERIC:
		$CI->output->set_status_header(500);
		$body = [
			"message"=> $customError ?? "Something broke, please retry later.", 
			"errorCode"=>$errored,
		];
		break;
	case RP_ERROR_NOT_PROCESSABLE:
		$CI->output->set_status_header(422);
		$body = [
			"message"   => $customError ?? "Request is invalid.",
			"errorCode" => $errored,
		];
		break;
	case RP_ERROR_CONFLICT:
		$CI->output->set_status_header(409);
		$body = [
			"messages" => [
				"A conflict occurred and thus the operation couldn't be executed",
				"Sorry,The operation couldn't be executed.",
				$customError ?? "Something wend wrong, please try again later."
			],
			"errorCode"=>RP_ERROR_CONFLICT
		];
		break;
	case RP_ERROR_NOT_FOUND:
		$CI->output->set_status_header(404);
		$body = [
			"messages" => [$customError ?? "Not found"],
			"errorCode" => RP_ERROR_NOT_FOUND
		];
	break;
	case RP_ERROR_NONE:
		$body = array();
		if($newItemCreated ?? false){
			$body = [
				//this contains some nice messages that can be displayed
				//if the client wishes to stay on the same page
				"messages" => [
					"The item is successfully created",
					"The ".$resourceKind." is successfully created",
					$resourceName." is successfully created",
				],
				//same as the location header.
				"link" => base_url("index.php/api/".$urlPart),
				"name" => $resourceName,
				"kind" => $resourceKind,
				"pref" => $pref,
				"id"   => $id,
			];
		}
		if($data ?? false ){
			$body["data"] = $data;
		}
		if($correctReturn===201){
			$CI->output->set_header("Location: ".$body["link"]);
			unset($body["link"]);
		}
		$CI->output->set_status_header($correctReturn);
		break;
	default:
		//something very weird happened....
		//a generic error happened :(
		$CI->output->set_header(500);
		$body = [
			"message"=>"This shouldn't have happened..... :('",
			"errorCode"=>$errored
		];
}
$CI->outputPlusFilter($body)->_display();
die();
