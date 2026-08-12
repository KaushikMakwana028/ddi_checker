<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
*/
$route['default_controller']   = 'admin/Login/index';
$route['404_override']        = '';
$route['translate_uri_dashes'] = FALSE;

// =========================================================================
// ADMIN SIDE ROUTES
// =========================================================================
$route['admin']                         = 'admin/Login/login';
$route['admin/login']                   = 'admin/Login/login';
$route['admin/register']                = 'admin/Login/register';
$route['admin/logout']                  = 'admin/Login/logout';

$route['admin/dashboard']               = 'admin/Dashboard/index';

$route['admin/profile']                 = 'admin/Profile/index';
$route['admin/profile/update']          = 'admin/Profile/update';
$route['admin/profile/change_password'] = 'admin/Profile/change_password';
$route['admin/profile/(:any)']          = 'admin/Profile/$1';

$route['admin/drug-entry']                      = 'admin/DrugEntry/index';
$route['admin/drug-entry/(:any)/(:any)']        = 'admin/DrugEntry/$1/$2';
$route['admin/drug-entry/(:any)']               = 'admin/DrugEntry/$1';

$route['admin/interactions']                    = 'admin/InteractionRules/index';
$route['admin/interactions/(:any)/(:any)']      = 'admin/InteractionRules/$1/$2';
$route['admin/interactions/(:any)']             = 'admin/InteractionRules/$1';
$route['admin/interaction-rules']               = 'admin/InteractionRules/index';
$route['admin/interaction-rules/(:any)/(:any)'] = 'admin/InteractionRules/$1/$2';
$route['admin/interaction-rules/(:any)']        = 'admin/InteractionRules/$1';

$route['admin/doctors']                         = 'admin/DoctorManage/index';
$route['admin/doctors/(:any)/(:any)']           = 'admin/DoctorManage/$1/$2';
$route['admin/doctors/(:any)']                  = 'admin/DoctorManage/$1';

// =========================================================================
// DOCTOR PORTAL ROUTES
// =========================================================================
$route['doctor']                        = 'doctor/Login/login';
$route['doctor/login']                  = 'doctor/Login/login';
$route['doctor/register']               = 'doctor/Login/register';
$route['doctor/logout']                 = 'doctor/Login/logout';
$route['doctor/dashboard']              = 'doctor/Dashboard/index';
