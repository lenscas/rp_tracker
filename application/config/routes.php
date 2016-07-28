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
$route['default_controller'] = 'welcome';
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
//ajax
	//user
		$route["ajax/login"]			=	"json/Users/login";
		$route['ajax/register']			=	"json/Users/register";
		$route['ajax/profile/(:any)']	=	"json/Users/profile/$1";
	//characters
		$route['ajax/character/list/(:any)']	=	"json/Char/getCharList/$1";
		$route['ajax/character/(:any)']			=	"json/Character/show/$1/false"; //need to checkout what this was for it seems. 
		$route['ajax/rp/abilityList/(:any)']	=	"json/Char/getAbilitiesByCharInRP/$1";
	//rp
		$route["ajax/rp/create"]				=	"json/Rp/create";
		$route['ajax/rp/getAllRPs']				=	"json/Rp/listAllRPs";
		$route['ajax/rp/details/(:any)']		=	"json/Rp/getRP/$1";
		$route['ajax/rp/join/(:any)']			=	"json/Rp/join/$1";
		$route['ajax/rp/getCharacter/(:any)']	=	"json/Char/getCharacter/$1";
		$route['ajax/rp/getConfig/(:any)']		=	"json/Rp/getRPConfig/$1";
		$route['ajax/rp/getAllStatSheets']		=	"json/Rp/getAllStatSheets";
		
