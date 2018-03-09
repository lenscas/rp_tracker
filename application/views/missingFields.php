<?php
$CI =& get_instance();
$CI->output->set_status_header(422);
$body = [
	"messages" => [
		"One or more required fields where not filled in correctly.",
		"[ERROR]",
		"Sorry, but the following errors have been found.\n [ERRORS]"
	],
	"errors" => $CI->form_validation->error_array()
];
if($CI->session->userId){
	$body["userId"] = $CI->session->userId;
}
$CI->outputPlusFilter($body)->_display();
//we don't want the program to do other stuff. So....lets die
die();
