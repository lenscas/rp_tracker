<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'browser/Users/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
//browser
	//user
		//user creation
		$route['register']			=	"browser/Users/register";
		$route['register/success']	=	"browser/Users/showMade";
		$route["activation/(:any)"]	=	"browser/Users/activate/$1";
		//user logout/in
		$route['login']		=	"browser/Users/login";
		$route['logout']	=	"browser/Users/logout";
		//shwoing userData data
		$route['profile']			=	"browser/Users/profile";
		$route['profile/(:any)']	=	"browser/Users/profile/$1";
		$route['character/(:any)']	=	"browser/Users/character/$1";
	//rp
		$route['rp/create']					=	"browser/Rp/create";
		//$route['rp/success/(:any)']			=	"browser/Rp/createChar/$1";
		
		$route['rp/list']					=	"browser/Rp/showAllRPs";
		$route['rp/details/(:any)']			=	"browser/Rp/getRpDetails/$1";
	//character 
		$route['rp/character/create/(:any)']=	"browser/Char/createChar/$1";
		$route['rp/character/view/(:any)']	=	"browser/Char/character/$1";
		$route['rp/character/list/(:any)']	=	"browser/Char/charList/$1";
	//battle
		$route['rp/battle/create/(:any)']	=	"browser/Battle/create/$1";
		$route['rp/battle/list/(:any)']		=	"browser/Battle/battleList/$1";
		$route['rp/battle/manage/(:any)']	=	"browser/Battle/manageBattle/$1";
//api
	//user
		$route["api/login"]["POST"]           =  "api/Users/login";
		$route['api/users/(:any)']["GET"]     =  "api/Users/profile/$1";
		$route["api/users"]["POST"]           =  "api/Users/register";
		//$route['api/profile/(:any)'] =  "api/Users/profile/$1";
	//characters
		//the first (:any) refers to the rp_code
		$route["api/characters/(:any)"]["GET"]        =  "api/Char/getCharList/$1";
		$route["api/characters/(:any)"]["POST"]       =  "api/Char/CreateCharacter/$1"; //TODO not yet implemented
		$route['api/characters/(:any)/(:any)']["GET"] =  "api/Char/getCharacter/$1/$2";
		
		
		//$route['api/character/(:any)']      =  "api/Character/show/$1/false"; //need to checkout what this was for it seems. 
	//ability list
		$route['api/abilities/(:any)']["GET"]  =  "api/Char/getAbilitiesByCharInRP/$1";
	//rp
		$route["api/rp"]["POST"]            =  "api/Rp/create";
		$route['api/rp']["GET"]             =  "api/Rp/listAllRPs";
		$route['api/rp/(:any)']["GET"]      =  "api/Rp/getRP/$1";
		$route['api/join/(:any)']["GET"]    =  "api/Rp/join/$1";
		$route["api/config/(:any)"]["GET"]  =  "api/Rp/getRPConfig/$1";
		$route["api/statsheet"]["GET"]      =  "api/Rp/getAllStatSheets";
		//$route['api/rp/join/(:any)']        =  "api/Rp/join/$1";
		//$route['api/rp/getAllStatSheets']   =  "api/Rp/getAllStatSheets";
		//$route['api/rp/getCharacter/(:any)']  =  "api/Char/getCharacter/$1";
		//$route['api/rp/getConfig/(:any)']     =  "api/Rp/getRPConfig/$1";
	//modifiers
		$route['api/modifiers/(:any)']["PUT"]     =  "api/Modifiers/updateModifier/$1";
		$route['api/modifiers/(:any)']["POST"]    =  "api/Modifiers/insertModifier/$1";
		$route['api/modifiers/(:any)']["DELETE"]  =  "api/Modifiers/deleteModifier/$1";
	//battle
		$route['api/battle/(:any)']["POST"]               =  "api/Battle/createBattle";
		$route['api/battle/(:any)']["GET"]         =  "api/Battle/getAllBattlesByRp/$1";
		$route['api/battle/(:any)/(:any)']["GET"]  =  "api/Battle/getBattle/$1/$2";
	
