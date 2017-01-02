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
		$route["api/login"]          =  "api/Users/login";
		$route['api/users']          =  "api/Users/users";
		//$route['api/profile/(:any)'] =  "api/Users/profile/$1";
	//characters
		$route["api/characters/(:any)"]     =  "api/Char/character/$1";
		
		//$route['api/character/list/(:any)'] =  "api/Char/getCharList/$1";
		//$route['api/character/(:any)']      =  "api/Character/show/$1/false"; //need to checkout what this was for it seems. 
	//ability list
		$route['api/rp/abilityList/(:any)'] =  "api/Char/getAbilitiesByCharInRP/$1";
	//rp
		$route["api/rp/create"]               =  "api/Rp/create";
		$route['api/rp/getAllRPs']            =  "api/Rp/listAllRPs";
		$route['api/rp/details/(:any)']       =  "api/Rp/getRP/$1";
		$route['api/rp/join/(:any)']          =  "api/Rp/join/$1";
		$route['api/rp/getCharacter/(:any)']  =  "api/Char/getCharacter/$1";
		$route['api/rp/getConfig/(:any)']     =  "api/Rp/getRPConfig/$1";
		$route['api/rp/getAllStatSheets']     =  "api/Rp/getAllStatSheets";
	//modifiers
		$route['api/modifiers/update/(:any)']  =  "api/Modifiers/updateModifier/$1";
		$route['api/modifiers/create/(:any)']  =  "api/Modifiers/insertModifier/$1";
		$route['api/modifiers/delete/(:any)']  =  "api/Modifiers/deleteModifier/$1";
	//battle
		$route['api/battle/create']                =  "api/Battle/createBattle";
		$route['api/battle/getAllBattles/(:any)']  =  "api/Battle/getAllBattlesByRp/$1";
		$route['api/battle/getBattle/(:any)']      =  "api/Battle/getBattle/$1";
	
