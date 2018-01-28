<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lua_model extends MY_model {
	public $modes = [
		"NOTHING" => 0,
		"INSERT"  => 1,
		"DELETE"  => 2,
		"UPDATE"  => 3,
		
	];
	public $kinds = [
		"OUTPUT"    => 0,
		"ERROR"     => 1,
		"MODIFIER"  => 2,
		"CHARACTER" => 3,
	];
	private $luaScriptPath = APPPATH . "/lua_script_parts/basicSetup.lua";
	public function __construct(){
		parent::__construct();
	}
	public function runAction($rpCode,$battleId,$config,$actions){
		if(!class_exists("Lua")){
			return ["success"=>false,"PHP-Lua module not found."];
		}
		$this->load->model("Battle_model");
		$rawBattle = $this->Battle_model->getBattle($rpCode,$battleId,true);
		$battle    = (array)$rawBattle["battle"];
		$battle["characters"] = array();
		foreach($rawBattle["characters"] as $charKey=>$charValue){
			$modifiers= array();
			foreach($rawBattle["modifiers"] as $modKey=>$modValue){
				$modifiers[$modValue["intName"]] = $modifiers[$modValue["intName"]] ?? array();
				$modifiers[$modValue["intName"]][] = $modValue;
			}
			$charData = (array)$charValue;
			$charData["modifiers"] = $modifiers;
			unset($charData["id"]);
			$battle["characters"][$charData["code"]]=$charData;
		}
		$lua = new Lua();
		$deltaCont     = &$this->registerReturnFunction($lua);
		$actionScript  = $this->addHelpers($rpCode);
		$actionScript .= $this->addActions($actions);
		$actionScript .= $this->addRunAction(
			$config["action"],
			$config["user"],
			$config["target"]
		);
		$this->registerVars($lua,["battleEnv"=>$battle,"actionScript"=>$actionScript]);
		$luaScript = file_get_contents($this->luaScriptPath);
		$error = false;
		try{
			$lua->eval($luaScript);
		} catch(Exception $e) {
			$error = (string)$e;
		}
		$returnData = [
			"success"=> true,
			"data"   => [
				"deltas"=>$this->real_array_shift_by_one( $deltaCont)
			],
		];
		if($error){
			$returnData["success"] = false;
			$returnData["error"]   = $error;
			$returnData["script"]  = $actionScript;
		}
		return $returnData;
	}
	private function addRunAction($action,$user,$target){
		return 'actions["' . $action . '"](
			battle:getCharacterByCode("'.$user.'"),
			battle:getCharacterByCode("'.$target.'")
		)';
	}
	private function addHelpers($rpCode){
		$helpers = $this->db->select(
			"helper_functions.name,helper_functions.params,helper_functions.code"
		)->from("rolePlays")
		->join("helper_functions","rolePlays.battleSystemId=helper_functions.battleSystemId")
		->where("rolePlays.code",$rpCode)
		->get()
		->result();
		$str = "local helpers = {}\n";
		foreach($helpers as $key=>$value){
			$str .= 'helpers["'.$value->name .'"] =function('.$value->params .")\n".$value->code ."\n end \n";
		}
		return $str;
	}
	private function addActions($actions){
		$str = "local actions = {}\n";
		foreach($actions as $key=>$value){
			$str .= 'actions["'. $value->name .'"] =function(user,target,...)'. "\n". $value->code . "\n end \n";
		}
		return $str;
	}
	private function registerVars($lua,$varList){
		$varNames = [];
		$lua->assign("kinds",$this->kinds);
		$lua->assign("modes",$this->modes);
		foreach($varList as $key=>$value){
			$lua->assign($key,$value);
		}
	}
	private function real_array_shift_by_one($array){
		$newArray = array();
		foreach($array as $key=>$value){
			$newArray[$key-1] = $value;
		}
		return $newArray;
	}
	private function &registerReturnFunction($lua){
		$deltas = array();
		$lua->registerCallback("returnDeltas",function($returnedDeltas) use(&$deltas){
			$deltas = $returnedDeltas;
		});
		return $deltas;
	}
}
