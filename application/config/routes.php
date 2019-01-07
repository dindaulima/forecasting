<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// $route['scsiuji/forecast'] = 'scsiuji/forecast';
// $route['scsi/fst'] = 'scsi/fst';
// $route['scsi/(:any)'] = 'scsi/view/$1';
// $route['scsi'] = 'scsi/index';
$route['default_controller'] = 'pages/view';
$route['(:any)'] = 'pages/view/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
