<?php
class NoValidCode extends Exception{
	public $newCode;

	public function __construct(
		$newCode,
		$message = "",
		$code = 0,
		Throwable $previous = NULL
	){
		$this->newCode = $newCode;
	}
}
class Output{
	const CODES = [
		"NO_ERROR"          => 200,
		"CREATED"           => 201,
		"DUPLICATE"         => 409,
		"NO_PERMISSION"     => 2,
		"WRONG_INFORMATION" => 422,
		"NOT_FOUND"         => 404,
		"GENERIC_ERROR"     => 500,
		"DEVELOPER_ERROR"   => 500
	];
	private $CI;
	private $code;
	public $hooks;

	public function __construct(){
		$this->CI = &get_instance();
		$this->code = Output::CODES["NO_ERROR"];
		$this->hooks = [];
	}
	public function getCode(){return $this->code;}
	public function setCode(string $code){
		if(!in_array($code, Output::CODES)){
			throw new NoValidCode($code);
		}
		$this->code = $code;
		return $this;
	}
	public function add(string $name,$value){
		$this->data[$name] = $value;
		return $this;
	}
	public function __unset(string $name){
		unset($this->data[$name]);
	}
	public function addRenderHook(callable $hook){
		$this->hooks[] = $hook;
	}
	public function render(bool $sendUserIdIfAble = true){
		$returnData = [
			"data"     => $this->data ?? []
		];

		if(!$this->CI){
			$this->setCode(Output::CODES["DEVELOPER_ERROR"]);
		} elseif($this->CI->user->checkIsLoggedIn() && $sendUserIdIfAble) {
			$returnData["userId"] = $this->CI->user->getIdForced();
		}
		http_response_code($this->code);
		header("Content-Type: application/json");
		echo json_encode($returnData);
		$this->CI->output->_display();
		foreach($this->hooks as $key=>$value){
			call_user_func($value);
		}
		die();
	}
	public function doCallAndRender(callable $func,string $name,array $params=[]){
		$data = call_user_func_array($func,$params);
		$this->add($name,$data)->render();
	}
}
