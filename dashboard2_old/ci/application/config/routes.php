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


/*
 * #####################################################################################################################
 * APP FUNCTIONS
 */
$route['get_app_cfg']['post'] = 'AppController/getAppCfg';
$route['get_functions']['post'] = 'AppController/getFunctions';

/*
 * #####################################################################################################################
 * COMMON
 */
$route['common/regions']['post'] = 'ZoneController/getRegions';
$route['common/provinces']['post'] = 'ZoneController/getProvinces';
$route['common/defect']['post'] = 'CommonController/getDefectCode';
$route['common/suppliers']['post'] = 'CommonController/getSuppliers';
$route['common/stations']['post'] = 'CommonController/getStationsUserList';

/*
 * #####################################################################################################################
 * LOGIN - LOGOUT
 */
$route['login']['post'] = 'LoginController/doLogin';
$route['logout']['post'] = 'LoginController/doLogout';

/*
 * #####################################################################################################################
 * USERS
 */
$route['users/list']['post'] = 'UsersController/getUsers';
$route['users/add']['post'] = 'UsersController/addUser';
$route['users/edit']['post'] = 'UsersController/editUser';
$route['users/profile/edit']['post'] = 'UsersController/editUserProfile';
$route['users/delete/(:num)']['get'] = 'UsersController/delUser/$1';
$route['confirm/(:num)/(:any)']['get'] = 'UsersController/confirmEmail/$1/$2';
$route['recovery']['post'] = 'UsersController/Recovery';

/*
 * #####################################################################################################################
 * PROFILES
 */
$route['profiles/list/all']['post'] = 'ProfilesController/getProfilesAll'; // for combo
$route['profiles/list']['post'] = 'ProfilesController/getProfilesList'; // for grid
$route['profiles/functions/(:num)']['post'] = 'ProfilesController/getFunctions/$1';
$route['profiles/add']['post'] = 'ProfilesController/addProfile';
$route['profiles/edit']['post'] = 'ProfilesController/editProfile';
$route['profiles/del/(:num)']['get'] = 'ProfilesController/delProfile/$1';
$route['profiles/en_profile/(:num)/(:num)']['get'] = 'ProfilesController/enableProfile/$1/$2';
$route['profiles/dis_profile/(:num)/(:num)']['get'] = 'ProfilesController/disableProfile/$1/$2';


/*
 * #####################################################################################################################
 * HOME
 */
$route['home/messages']['post'] = 'HomeController/getMessages';
$route['home/reports']['post'] = 'HomeController/getHomeReports';

/*
 * #####################################################################################################################
 * DASHBOARD
 */
$route['dashboard/daily/total']['post'] = 'DashboardController/getDailyTotal';
$route['dashboard/daily/pratices']['post'] = 'DashboardController/getDailyPratices';
$route['dashboard/daily/irm']['post'] = 'DashboardController/getDailyIrm';
$route['dashboard/year/total']['post'] = 'DashboardController/getYearTotal';
$route['dashboard/year/pratices']['post'] = 'DashboardController/getYearPratices';
$route['dashboard/year/irm']['post'] = 'DashboardController/getYearIrm';

/*
 * #####################################################################################################################
 * PRATICES
 */
$route['pratices/edit/setmanually/(:num)']['get'] = 'PraticesController/setManually/$1';

// daily pratices
$route['pratices/daily/list']['post'] = 'PraticesController/getDailyList';
$route['pratices/daily/list/exportall']['post'] = 'ExportController/getDailyList';

// archive
$route['pratices/archive/all']['post'] = 'PraticesController/getArchiveAll';
$route['pratices/archive/all/exportall']['post'] = 'ExportController/getArchiveAll';
$route['pratices/archive/automated']['post'] = 'PraticesController/getArchiveAutomated';
$route['pratices/archive/automated/exportall']['post'] = 'ExportController/getArchiveAutomated';
$route['pratices/archive/manually']['post'] = 'PraticesController/getArchiveManually';
$route['pratices/archive/manually/exportall']['post'] = 'ExportController/getArchiveManually';
$route['pratices/archive/rejected']['post'] = 'PraticesController/getArchiveRejected';
$route['pratices/archive/rejected/exportall']['post'] = 'ExportController/getArchiveRejected';

// picked/not picked/
$route['pratices/picked/list']['post'] = 'PraticesController/getPicked';
$route['pratices/picked/list/exportall']['post'] = 'ExportController/getPicked';
$route['pratices/notpicked/list']['post'] = 'PraticesController/getNotPicked';
$route['pratices/notpicked/list/exportall']['post'] = 'ExportController/getNotPicked';
$route['pratices/waiting/list']['post'] = 'PraticesController/getWaiting';
$route['pratices/waiting/list/exportall']['post'] = 'ExportController/getWaiting';

/*
 * #####################################################################################################################
 * ROBOT
 */
$route['robot/status']['post'] = 'RobotController/getRobotStatus';
$route['robot/switchon']['get'] = 'RobotController/robotPowerOn';
$route['robot/switchoff']['get'] = 'RobotController/robotPowerOff';
$route['robot/action/(:any)']['get'] = 'RobotController/doAction/$1';

/*
 * #####################################################################################################################
 * AVAILABILITY
 */
$route['availability/getdata']['post'] = 'AvailabilityController/getData';

/*
 * #####################################################################################################################
 * EXPORT DATA
 */
$route['export/pratices/excel']['post'] = 'ExportController/exportPraticesExcel';
$route['export/pratices/csv']['post'] = 'ExportController/exportPraticesCsv';


/*
 * #####################################################################################################################
 * REPORTS
 */
$route['reports/suppliers/permanence/avg']['post'] = 'ReportsController/getSuppliersPermanenceAvg';
$route['reports/suppliers/engagepickup/avg']['post'] = 'ReportsController/getSuppliersEngagePickupAvg';
$route['reports/suppliers/kpi']['post'] = 'ReportsController/getSuppliersKpi';
$route['reports/vehicles/kpi']['post'] = 'ReportsController/getVehiclesKpi';
$route['reports/vehicles/kpi/numbers']['post'] = 'ReportsController/getVehiclesKpiNumbers';
$route['reports/vehicles/damagerepair/avg']['post'] = 'ReportsController/getDamageRepairAvg';
$route['reports/vehicles/checkinirm/avg']['post'] = 'ReportsController/getCheckinIrmAvg';
$route['reports/robot/kpi']['post'] = 'ReportsController/getRobotKpi';
$route['reports/robot/working/avg']['post'] = 'ReportsController/getRobotWorkingAvg';
$route['reports/robot/endwsm/avg']['post'] = 'ReportsController/getRobotEndWsmAvg';
//$route['report/pratices/avgstations']['post'] = 'ReportsController/getAvgStationWorkingTime';
$route['report/pratices/avgallstationschart']['post'] = 'ReportsController/getAvgStationWorkingTimeChart';
$route['report/pratices/avgsinglestationschart']['post'] = 'ReportsController/getSingleAvgStationWorkingTimeChart';
$route['report/pratices/allavgstartend']['post'] = 'ReportsController/getAllAvgStartEnd';
$route['report/pratices/singleavgstartend']['post'] = 'ReportsController/getSingleAvgStartEnd';
$route['report/pratices/allavgendwsm']['post'] = 'ReportsController/getAllAvgEndWsm';
$route['report/pratices/singleavgendwsm']['post'] = 'ReportsController/getSingleAvgEndWsm';
$route['report/pratices/allavgciirm']['post'] = 'ReportsController/getAllAvgCiIrm';
$route['report/pratices/singleavgciirm']['post'] = 'ReportsController/getSingleAvgCiIrm';
$route['report/pratices/allavgciwsm']['post'] = 'ReportsController/getAllAvgCiWsm';
$route['report/pratices/singleavgciwsm']['post'] = 'ReportsController/getSingleAvgCiWsm';

