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
//api
	//user
		$route['api/logout']                  =  "api/Users/logout";
		$route["api/login"]["POST"]           =  "api/Users/login";
		$route['api/users/(:any)']["GET"]     =  "api/Users/profile/$1";
		$route["api/users/(:any)/username"]["GET"] = "api/users/getName/$1";
		$route["api/users"]["POST"]           =  "api/Users/register";
		 
		//$route['api/profile/(:any)'] =  "api/Users/profile/$1";
	//characters
		$route["api/rp/(:any)/characters"]["GET"]           = "api/Char/getCharList/$1";
		$route["api/rp/(:any)/characters"]["POST"]          = "api/Char/CreateCharacter/$1";
		$route["api/rp/(:any)/characters/(:any)"]["GET"]    = "api/Char/getCharacter/$1/$2";
		$route["api/rp/(:any)/characters/(:any)/userId"]["GET"] = "api/Char/GetUserIdFromCharCode/$1/$2";
		//this one has no old equivalant and will not get one.
		$route["api/rp/(:any)/characters/(:any)"]["PATCH"]  = "api/Char/patchCharacter/$1/$2";
	//ability list
		$route["api/rp/(:any)/abilities"]["GET"] = "api/Char/getAbilitiesByCharInRP/$1";
		$route['api/rp/(:any)/characters/(:any)/abilities']["GET"]  =  "api/Char/getAbilitiesByCharInRP/$2";
	//rp
		$route["api/rp"]["POST"]              =  "api/Rp/create";
		$route['api/rp']["GET"]               =  "api/Rp/listAllRPs";
		$route['api/rp/(:any)']["GET"]        =  "api/Rp/getRP/$1";
		$route['api/rp/(:any)/join']["GET"]   =  "api/Rp/join/$1";
		$route["api/rp/(:any)/config"]["GET"] =  "api/Rp/getRPConfig/$1";

	//modifiers
		$route["api/rp/(:any)/characters/(:any)/modifiers/(:any)"]["PUT"]    = "api/Modifiers/updateModifier/$1/$2/$3";
		$route["api/rp/(:any)/characters/(:any)/modifiers"]["POST"]          = "api/Modifiers/insertModifier/$1/$2";
		$route["api/rp/(:any)/characters/(:any)/modifiers/(:any)"]["DELETE"] = "api/Modifiers/deleteModifier/$1/$2/$3";

	//actions
		$route["api/rp/(:any)/actions"]["GET"] = "api/Actions/getAllActions/$1";
		$route["api/rp/(:any)/battles/(:any)/actions/(:any)/run"]["POST"] = "api/Actions/runAction/$1/$2/$3";
		$route["api/rp/(:any)/battles/(:any)/env"]["PUT"] = "api/Battle/saveDeltas/$1/$2";
	//battle Systems
		$route["api/system"]["GET"] = "api/Battle/getAllBattleSystems";
	//battle
		$route['api/rp/(:any)/battles']["POST"]        =  "api/Battle/createBattle/$1";
		$route['api/rp/(:any)/battles']["GET"]         =  "api/Battle/getAllBattlesByRp/$1";
		$route['api/rp/(:any)/battles/(:any)']["GET"]  =  "api/Battle/getBattle/$1/$2";
		$route["api/rp/(:any)/battles/(:any)/users"]["GET"] = "api/Battle/getAllUsersInBattle/$1/$2";
		$route["api/rp/(:any)/battles/(:any)/nextTurn"]["POST"] = "api/Battle/nextTurn/$1/$2";
	//socket
		$route["api/socket/config"]["GET"] = "api/Socket/getConfig";
		$route["api/socket/check/users/(:any)"]["GET"]  = "api/Socket/checkRegisterCode/$1";
