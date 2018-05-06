<?php
$CI =& get_instance();
$CI->load->helper("easy_output");
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
outputPlusFilter($body)->_display();
die();
