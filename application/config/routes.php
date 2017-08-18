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
		$route['api/logout']                      =  "browser/Users/logout";
		$route["api/login"]["POST"]           =  "api/Users/login";
		$route['api/users/(:any)']["GET"]     =  "api/Users/profile/$1";
		$route["api/users"]["POST"]           =  "api/Users/register";
		//$route['api/profile/(:any)'] =  "api/Users/profile/$1";
	//characters
		//the first (:any) refers to the rp_code
		//the old routes. THESE WILL BE REMOVED!
		$route["api/characters/(:any)"]["GET"]        =  "api/Char/getCharList/$1";
		$route["api/characters/(:any)"]["POST"]       =  "api/Char/CreateCharacter/$1"; //TODO not yet implemented
		$route['api/characters/(:any)/(:any)']["GET"] =  "api/Char/getCharacter/$1/$2";
		//the new and better routes. These are the ones you want to use
		//these are not yet tested but should work.
		$route["api/rp/(:any)/characters"]["GET"]           = "api/Char/getCharList/$1";
		$route["api/rp/(:any)/characters"]["POST"]          = "api/Char/CreateCharacter/$1";
		$route["api/rp/(:any)/characters/(:any)"]["GET"]    = "api/Char/getCharacter/$1/$2";
		//this one has no old equivalant and will not get one.
		$route["api/rp/(:any)/characters/(:any)"]["PATCH"]  = "api/Char/patchCharacter/$1/$2";
	//ability list
		$route["api/rp/(:any)/abilities"]["GET"] = "api/Char/getAbilitiesByCharInRP/$1";
		$route['api/abilities/(:any)']["GET"]  =  "api/Char/getAbilitiesByCharInRP/$1";
		$route['api/rp/(:any)/characters/(:any)/abilities/']["GET"]  =  "api/Char/getAbilitiesByCharInRP/$2";
	//rp
		$route["api/rp"]["POST"]              =  "api/Rp/create";
		$route['api/rp']["GET"]               =  "api/Rp/listAllRPs";
		$route['api/rp/(:any)']["GET"]        =  "api/Rp/getRP/$1";
		$route['api/rp/(:any)/join']["GET"]   =  "api/Rp/join/$1";
		$route["api/rp/(:any)/config"]["GET"] =  "api/Rp/getRPConfig/$1";
		$route["api/statsheet"]["GET"]        =  "api/Rp/getAllStatSheets";
		//$route['api/rp/join/(:any)']        =  "api/Rp/join/$1";
		//$route['api/rp/getAllStatSheets']   =  "api/Rp/getAllStatSheets";
		//$route['api/rp/getCharacter/(:any)']  =  "api/Char/getCharacter/$1";
		//$route['api/rp/getConfig/(:any)']     =  "api/Rp/getRPConfig/$1";
	//modifiers
		$route["api/rp/(:any)/characters/(:any)/modifiers/(:any)"]["PUT"] = "api/Modifiers/updateModifier/$3";
		$route["api/rp/(:any)/characters/(:any)/modifiers"]["POST"] = "api/Modifiers/insertModifier/$2";
		$route["api/rp/(:any)/characters/(:any)/modifiers/(:any)"]["DELETE"] = "api/Modifiers/deleteModifier/$3";
		//old routes.
		$route['api/modifiers/(:any)']["PUT"]     =  "api/Modifiers/updateModifier/$1";
		$route['api/modifiers/(:any)']["POST"]    =  "api/Modifiers/insertModifier/$1";
		$route['api/modifiers/(:any)']["DELETE"]  =  "api/Modifiers/deleteModifier/$1";
	//battle
		$route['api/rp/(:any)/battles']["POST"]        =  "api/Battle/createBattle/$1";
		$route['api/rp/(:any)/battles']["GET"]         =  "api/Battle/getAllBattlesByRp/$1";
		$route['api/rp/(:any)/battles/(:any)']["GET"]  =  "api/Battle/getBattle/$1/$2";
	
