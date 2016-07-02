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
$route['default_controller'] = 'navigation';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
//Anouncement Controller
$route['announcement'] = 'announcement';
$route['announcement/create'] = 'announcement/create';
if(ENVIRONMENT == 'development') $route['announcement/create/batch'] = 'announcement/create_batch';
$route['announcement/update'] = 'announcement/update';
$route['announcement/update/(.+)'] = 'announcement/update/$1';
$route['announcement/schedule'] = 'announcement/schedule';
$route['announcement/verify'] = 'announcement/verify';
$route['announcement/verify/(.+)'] = 'announcement/verify/$1/$2';
$route['announcement/delete'] = 'announcement/delete';
$route['announcement/delete/all'] = 'announcement/delete_all';
$route['announcement/display'] = 'announcement/display';
//Anouncement Controller Methods (no direct access allowed)
$route['announcement/get_calendar'] = 'announcement/get_calendar';
$route['announcement/get_calendar/(.+)'] = 'announcement/get_calendar';
$route['announcement/check_datetime'] = 'announcement/get_calendar';
$route['announcement/check_datetime/(.+)'] = 'announcement/get_calendar';
$route['announcement/display/update'] = 'announcement/update_list';
$route['announcement/display/update/weather'] = 'announcement/update_weather';
//Anouncement Controller General
$route['announcement/(:any)'] = 'announcement';
//User Controller
$route['user'] = 'user';
$route['user/create'] = 'user/create';
$route['user/create/batch'] = 'user/create_batch';
$route['user/update'] = 'user/update';
$route['user/update/(.+)'] = 'user/update/$1';
$route['user/delete'] = 'user/delete';
//User Controller Methods (no direct access allowed)
$route['user/verify_pass'] = 'user/verify_pass';
//User Controller General
$route['user/(:any)'] = 'user';
$route['login'] = 'user/login';
$route['logout'] = 'user/logout';
//Navigation Controller
$route['dashboard'] = 'navigation/load_page/dashboard/'.TRUE;
/*$route['asset'] = 'navigation/load_asset';
$route['asset/(.+)'] = 'navigation/load_asset';*/
//Navigation Controller General
$route['(:any)'] = 'navigation/load_page/$1';
$route['(:any)/(:)any)'] = 'navigation/load_page/$1/$2';