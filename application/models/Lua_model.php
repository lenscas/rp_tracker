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
		"NEXT_TURN" => 4,
	];
	private $luaScriptPath = APPPATH . "/lua_script_parts/basicSetup.lua";
	public function __construct(){
		parent::__construct();
	}
	private function luaSetup($rpCode,$battleId,$config,$actions){
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
				if($modValue["code"] == $charValue->code){
					$modifiers[$modValue["intName"]] = $modifiers[$modValue["intName"]] ?? array();
					$modifiers[$modValue["intName"]][] = $modValue;
				}
			}
			$charData = (array)$charValue;
			$charData["modifiers"] = $modifiers;
			unset($modifiers);
			unset($charData["id"]);
			$battle["characters"][$charData["code"]]=$charData;
		}
		//var_dump($battle);
		$lua = new Lua();
		$deltaCont     = &$this->registerReturnFunction($lua);
		$actionScript  = $this->addHelpers($rpCode);
		$actionScript .= $this->addActions($actions);
		$actionScript .= $this->addEnd($rpCode);
		return [
			"lua"       => $lua,
			"deltaCont" => &$deltaCont,
			"script"    => $actionScript,
			"battle"    => $battle
		];
	}
	public function luaExecute($luaSetup){
		$this->registerVars(
			$luaSetup["lua"],
			[
				"battleEnv"    => $luaSetup["battle"],
				"actionScript" => $luaSetup["script"]
			]
		);
		$luaScript = file_get_contents($this->luaScriptPath);
		$error = false;
		try{
			$luaSetup["lua"]->eval($luaScript);
		} catch(Exception $e) {
			var_dump($e);
			$error = (string)$e;
		}
		$returnData = [
			"success"=> true,
			"data"   => [
				"deltas"=>$this->real_array_shift_by_one($luaSetup["deltaCont"])
			],
		];
		if($error){
			$returnData["success"] = false;
			$returnData["error"]   = $error;
			$returnData["script"]  = $luaScript;
		}
		return $returnData;
	}
	public function runEnd($rpCode,$battleId,$config,$actions){
		$luaSetup = $this->luaSetup($rpCode,$battleId,$config,$actions);
		$luaSetup["script"] .= "nextTurn()\n";
		return $this->luaExecute($luaSetup);
	}
	public function runAction($rpCode,$battleId,$config,$actions){
		$luaSetup = $this->luaSetup($rpCode,$battleId,$config,$actions);
		$luaSetup["script"] .= $this->addRunAction(
			$config["action"],
			$config["user"],
			$config["target"]
		);
		return $this->luaExecute($luaSetup);
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
	private function addEnd($rpCode){
		$funcBody = $this->db->select("endFunction")
			->from("battleSystems")
			->join("rolePlays","rolePlays.battleSystemId=battleSystems.id")
			->where("rolePlays.code",$rpCode)
			->limit(1)
			->get()
			->row()
			->endFunction;
		return "function nextTurn()\n".$funcBody."\n end \n";
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
