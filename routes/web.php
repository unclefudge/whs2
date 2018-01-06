<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

// Site Login
Route::get('site/login/{site_id}', function ($site_id) {
    Session::put('siteID', $site_id);

    return redirect('/auth/login');
});



// Authentication routes...
Route::get('login', 'Auth\AuthController@getLogin')->name('login');
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
//Route::get('site/login', 'Auth\AuthController@getLoginSite');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@showResetForm');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

//Route::get('/home', 'HomeController@index');


Route::group(['middleware' => 'auth'], function () {
    // Pages
    Route::get('/', 'Misc\PagesController@index');
    Route::get('/dashboard', 'Misc\PagesController@index');
    Route::get('/manage/report', 'Misc\PagesController@reports');
    Route::get('/manage/report/newusers', 'Misc\PagesController@newusers');
    Route::get('/manage/report/newcompanies', 'Misc\PagesController@newcompanies');
    Route::get('/manage/report/users_noemail', 'Misc\PagesController@users_noemail');
    Route::get('manage/report/roleusers', 'Misc\PagesController@roleusers');
    Route::get('manage/report/users_extra_permissions', 'Misc\PagesController@usersExtraPermissions');
    Route::get('manage/report/missing_company_info', 'Misc\PagesController@missingCompanyInfo');
    Route::get('manage/report/company_users', 'Misc\PagesController@companyUsers');
    Route::get('/manage/quick', 'Misc\PagesController@quick');
    Route::get('/manage/fixplanner', 'Misc\PagesController@fixplanner');
    Route::get('/manage/importcompany', 'Misc\PagesController@importCompany');
    Route::get('/manage/completedqa', 'Misc\PagesController@completedQA');
    Route::get('/manage/create_permission', 'Misc\PagesController@createPermission');

    // Site Check-in

    // User Routes 
    Route::get('user/dt/users', 'UserController@getUsers');
    Route::get('user/dt/contractors', 'UserController@getContractors');
    Route::get('user/{username}/settings', 'UserController@showSettings');
    Route::post('user/{username}/settings/photo', 'UserController@updatePhoto');
    Route::post('user/{username}/settings/security', 'UserController@updateSecurity');
    Route::get('user/{username}/settings/security/permissions', 'UserController@getSecurityPermissions');
    Route::get('user/{username}/settings/security/permissions/reset/{role_id}', 'UserController@resetSecurityPermissions');
    Route::get('user/{username}/settings/{tab}', 'UserController@showSettings');
    Route::get('contractor', 'UserController@contractorList');
    Route::resource('user', 'UserController');

    // Company Leave Routes
    Route::get('company/leave/dt/leave', 'Company\CompanyLeaveController@getCompanyLeave');
    Route::resource('company/leave', 'Company\CompanyLeaveController');

    // Company Docs
    Route::get('company/doc/dt/docs', 'Company\CompanyDocController@getDocs');
    Route::get('company/doc/dt/expired', 'Company\CompanyDocController@getExpiredDocs');
    Route::any('company/doc/create', 'Company\CompanyDocController@create');
    Route::any('company/doc/upload', 'Company\CompanyDocController@upload');
    Route::post('company/doc/profile', 'Company\CompanyDocController@profile');
    //Route::get('company/doc/profile-reject/{id}', 'Company\CompanyDocController@profileReject');
    Route::get('company/doc/profile-destroy/{id}', 'Company\CompanyDocController@profileDestroy');
    Route::any('company/doc/export', 'Company\CompanyExportController@exportDocs');
    Route::post('company/doc/export/pdf', 'Company\CompanyExportController@docsPDF');
    Route::get('company/doc/create/tradecontract/{id}/{version}', 'Company\CompanyExportController@tradecontractPDF');
    Route::get('company/doc/create/subcontractorstatement/{id}/{version}', 'Company\CompanyExportController@subcontractorstatementPDF');
    Route::resource('company/doc', 'Company\CompanyDocController');

    // Company Routes
    Route::get('company/dt/companies', 'Company\CompanyController@getCompanies');
    Route::get('company/dt/staff', 'Company\CompanyController@getStaff');
    //Route::post('company/{id}/settings/logo', 'Company\CompanyController@updateLogo');
    Route::post('company/{id}/edit/logo', 'Company\CompanyController@updateLogo');
    Route::get('company/{id}/name', 'Company\CompanyController@getCompanyName');
    Route::get('company/{id}/approve', 'Company\CompanyController@approveCompany');
    Route::resource('company', 'Company\CompanyController');

    // Client Routes
    Route::get('client/dt/clients', 'Misc\ClientController@getClients');
    Route::get('client/{slug}/settings', 'Misc\ClientController@showSettings');
    Route::get('client/{slug}/settings/{tab}', 'Misc\ClientController@showSettings');
    Route::resource('client', 'Misc\ClientController');

    // File Manager
    Route::get('/manage/file', 'Misc\FileController@index');
    Route::get('/manage/file/directory', 'Misc\FileController@fileDirectory');
    Route::get('manage/file/directory/dt/docs', 'Misc\FileController@getDocs');

    // Site Hazards
    Route::get('site/hazard/dt/hazards', 'Site\SiteHazardController@getHazards');
    Route::get('site/hazard/{id}/status/{status}', 'Site\SiteHazardController@updateStatus');
    Route::resource('site/hazard', 'Site\SiteHazardController');
    //Route::resource('site/hazard-action', 'Site\SiteHazardActionController');

    // Site Accidents
    Route::get('site/accident/dt/accidents', 'Site\SiteAccidentController@getAccidents');
    Route::resource('site/accident', 'Site\SiteAccidentController');

    // Site Compliance
    Route::resource('site/compliance', 'Site\Planner\SiteComplianceController');

    // Site Docs
    Route::get('site/doc/type/{type}', 'Site\SiteDocController@listDocs');
    Route::get('site/doc/type/dt/{type}', 'Site\SiteDocController@getDocsType');
    Route::get('site/doc/dt/docs', 'Site\SiteDocController@getDocs');
    Route::any('site/doc/create', 'Site\SiteDocController@create');
    Route::any('site/doc/upload', 'Site\SiteDocController@upload');
    Route::resource('site/doc', 'Site\SiteDocController');

    // Site Quality Assurance
    Route::get('site/qa/{id}/items', 'Site\SiteQaController@getItems');
    Route::any('site/qa/{id}/update', 'Site\SiteQaController@updateReport');
    Route::any('site/qa/item/{id}', 'Site\SiteQaController@updateItem');
    Route::get('site/qa/company/{task_id}', 'Site\SiteQaController@getCompaniesForTask');
    Route::get('site/qa/dt/qa_reports', 'Site\SiteQaController@getQaReports');
    Route::get('site/qa/dt/qa_templates', 'Site\SiteQaController@getQaTemplates');
    Route::resource('site/qa', 'Site\SiteQaController');
    //Route::resource('site/qa/action', 'Site\SiteQaActionController');

    // Site Asbestos Register
    Route::get('site/asbestos/dt/list', 'Site\SiteAsbestosController@getReports');
    Route::get('site/asbestos/{id}/status/{status}', 'Site\SiteAsbestosController@updateStatus');
    Route::resource('site/asbestos', 'Site\SiteAsbestosController');

    // Report Actions
    Route::get('report/actions/{type}/{id}', 'Misc\ReportActionController@index');
    Route::post('report/actions/{type}/{id}', 'Misc\ReportActionController@store');
    Route::patch('report/actions/{type}/{id}', 'Misc\ReportActionController@update');

    Route::get('action/{table}/{table_id}', 'Misc\ActionController@index');
    Route::resource('action', 'Misc\ActionController');

    // Site Supervisors
    Route::get('site/supervisor/data/supers', 'Company\CompanySupervisorController@getSupers');
    Route::resource('site/supervisor', 'Company\CompanySupervisorController');

    // Site Exports
    Route::get('site/export', 'Site\Planner\SitePlannerExportController@index');
    Route::get('site/export/plan', 'Site\Planner\SitePlannerExportController@exportPlanner');
    Route::post('site/export/site', 'Site\Planner\SitePlannerExportController@sitePDF');
    Route::get('site/export/start', 'Site\Planner\SitePlannerExportController@exportStart');
    Route::post('site/export/start', 'Site\Planner\SitePlannerExportController@jobstartPDF');
    Route::get('site/export/completion', 'Site\Planner\SitePlannerExportController@exportCompletion');
    Route::post('site/export/completion', 'Site\Planner\SitePlannerExportController@completionPDF');
    Route::get('site/export/attendance', 'Site\Planner\SitePlannerExportController@exportAttendance');
    Route::post('site/export/attendance', 'Site\Planner\SitePlannerExportController@attendancePDF');
    Route::get('site/export/qa', 'Site\SiteQaController@exportQA');
    Route::post('site/export/qa', 'Site\SiteQaController@qaPDF');

    // Site Routes
    Route::get('site/dt/sites', 'Site\SiteController@getSites');
    Route::get('site/{slug}/checkin', 'Site\SiteController@siteCheckin');
    Route::post('site/{slug}/checkin', 'Site\SiteController@processCheckin');
    Route::get('site/{slug}/settings', 'Site\SiteController@showSettings');
    Route::post('site/{slug}/settings/admin', 'Site\SiteController@updateAdmin');
    Route::post('site/{slug}/settings/logo', 'Site\SiteController@updateLogo');
    Route::get('site/{slug}/settings/{tab}', 'Site\SiteController@showSettings');
    Route::get('site/data/details/{id}', 'Site\SiteController@getSiteDetails');
    //Route::get('site/data/owner/{id}', 'Site\SiteController@getSiteOwner');
    Route::resource('site', 'Site\SiteController');

    // Trade + Task Routes
    Route::resource('trade', 'Site\Planner\TradeController');
    Route::resource('task', 'Site\Planner\TaskController');

    // SDS Safety Docs
    Route::get('safety/doc/dt/sds', 'Safety\SdsController@getSDS');
    Route::any('safety/doc/sds/create', 'Safety\SdsController@create');
    Route::any('safety/doc/sds/upload', 'Safety\SdsController@upload');
    Route::resource('safety/doc/sds', 'Safety\SdsController');

    // Toolbox Talks
    Route::get('safety/doc/toolbox2', 'Safety\ToolboxTalkController@index');
    Route::get('safety/doc/toolbox2/{id}/accept', 'Safety\ToolboxTalkController@accept');
    Route::get('safety/doc/toolbox2/{id}/create', 'Safety\ToolboxTalkController@createFromTemplate');
    Route::get('safety/doc/toolbox2/{id}/reject', 'Safety\ToolboxTalkController@reject');
    Route::get('safety/doc/toolbox2/{id}/signoff', 'Safety\ToolboxTalkController@signoff');
    Route::get('safety/doc/toolbox2/{id}/archive', 'Safety\ToolboxTalkController@archive');
    Route::get('safety/doc/toolbox2/{id}/destroy', 'Safety\ToolboxTalkController@destroy');
    Route::post('safety/doc/toolbox2/{id}/upload', 'Safety\ToolboxTalkController@uploadMedia');
    Route::get('safety/doc/dt/toolbox2', 'Safety\ToolboxTalkController@getToolbox');
    Route::get('safety/doc/dt/toolbox_templates', 'Safety\ToolboxTalkController@getToolboxTemplates');
    Route::resource('safety/doc/toolbox2', 'Safety\ToolboxTalkController');


    // Safety Docs - WMS
    Route::get('safety/doc/wms', 'Safety\WmsController@index');
    Route::get('safety/doc/wms/expired', 'Safety\WmsController@expired');
    Route::get('safety/doc/wms/{id}/steps', 'Safety\WmsController@getSteps');
    Route::any('safety/doc/wms/{id}/update', 'Safety\WmsController@update');
    Route::get('safety/doc/wms/{id}/reject', 'Safety\WmsController@reject');
    Route::get('safety/doc/wms/{id}/signoff', 'Safety\WmsController@signoff');
    Route::get('safety/doc/wms/{id}/archive', 'Safety\WmsController@archive');
    Route::any('safety/doc/wms/{id}/pdf', 'Safety\WmsController@pdf');
    Route::post('safety/doc/wms/{id}/email', 'Safety\WmsController@email');
    Route::any('safety/doc/wms/{id}/upload', 'Safety\WmsController@upload');
    Route::get('safety/doc/wms/{id}/renew', 'Safety\WmsController@renew');
    Route::get('safety/doc/dt/wms', 'Safety\WmsController@getWms');
    Route::get('safety/doc/dt/wms_templates', 'Safety\WmsController@getWmsTemplates');
    Route::resource('safety/doc/wms', 'Safety\WmsController');

    // Roles / Permission
    Route::get('manage/role/permissions', 'Misc\RoleController@getPermissions');
    Route::get('manage/role/resetpermissions', 'Misc\PagesController@resetPermissions');
    Route::get('manage/role/child-primary/{id}', 'Misc\RoleController@childPrimary');
    Route::get('manage/role/child-default/{id}', 'Misc\RoleController@childDefault');
    Route::get('manage/role/parent', 'Misc\RoleController@parent');
    Route::get('manage/role/child', 'Misc\RoleController@child');
    Route::resource('manage/role', 'Misc\RoleController');

    // Configuration
    Route::resource('manage/settings/notifications', 'Misc\SettingsNotificationController');
    //Route::get('manage/settings/notifications', 'Company\CompanyNotificationController@show');
    //Route::post('manage/settings/notifications', 'Company\CompanyNotificationController@update');

    // Planners
    Route::any('planner/weekly', 'Site\Planner\SitePlannerController@showWeekly');
    Route::any('planner/site', 'Site\Planner\SitePlannerController@showSite');
    Route::any('planner/trade', 'Site\Planner\SitePlannerController@showTrade');
    Route::any('planner/attendance', 'Site\Planner\SitePlannerController@showAttendance');
    Route::any('planner/transient', 'Site\Planner\SitePlannerController@showTransient');
    Route::get('planner/data/sites', 'Site\Planner\SitePlannerController@getSites');
    Route::get('planner/data/site/{site_id}', 'Site\Planner\SitePlannerController@getSitePlan');
    Route::get('planner/data/site/{site_id}/attendance/{date}', 'Site\Planner\SitePlannerController@getSiteAttendance');
    Route::get('planner/data/site/{site_id}/allocate/{user_id}', 'Site\Planner\SitePlannerController@allocateSiteSupervisor');
    Route::any('planner/data/roster/user', 'Site\Planner\SitePlannerController@addUserRoster');
    Route::any('planner/data/roster/user/{id}', 'Site\Planner\SitePlannerController@delUserRoster');
    Route::any('planner/data/roster/add-company/{cid}/site/{site_id}/date/{date}', 'Site\Planner\SitePlannerController@addCompanyRoster');
    Route::any('planner/data/roster/del-company/{cid}/site/{site_id}/date/{date}', 'Site\Planner\SitePlannerController@delCompanyRoster');
    Route::any('planner/data/weekly/{date}/{super_id}', 'Site\Planner\SitePlannerController@getWeeklyPlan');
    Route::get('planner/data/company/{company_id}/tasks', 'Site\Planner\SitePlannerController@getCompanyTasks');
    Route::get('planner/data/company/{company_id}/tasks/trade/{trade_id}', 'Site\Planner\SitePlannerController@getCompanyTasks');
    Route::get('planner/data/company/{company_id}/trades', 'Site\Planner\SitePlannerController@getCompanyTrades');
    Route::get('planner/data/company/trade/{trade_id}', 'Site\Planner\SitePlannerController@getCompaniesWithTrade');
    Route::get('planner/data/company/{company_id}/trade/{trade_id}/site/{site_id}', 'Site\Planner\SitePlannerController@getCompanies');
    Route::get('planner/data/company/{company_id}/site/{site_id}/{date}', 'Site\Planner\SitePlannerController@getCompanySitesOnDate');
    Route::get('planner/data/trade', 'Site\Planner\SitePlannerController@getTrades');
    Route::get('planner/data/trade/upcoming/{date}', 'Site\Planner\SitePlannerController@getUpcomingTasks');
    Route::get('planner/data/trade/{trade_id}/tasks', 'Site\Planner\SitePlannerController@getTradeTasks');
    Route::get('planner/data/trade/jobstarts/{exists}', 'Site\Planner\SitePlannerController@getJobStarts');
    Route::get('planner/data/trade/joballocate', 'Site\Planner\SitePlannerController@getSitesWithoutSuper');
    Route::resource('planner', 'Site\Planner\SitePlannerController');

    // Support Tickets
    Route::get('support/ticket/dt/tickets', 'Support\SupportTicketController@getTickets');
    Route::get('support/ticket/dt/upgrades', 'Support\SupportTicketController@getUpgrades');
    Route::get('support/ticket/create', 'Support\SupportTicketController@create');
    Route::post('support/ticket/action', 'Support\SupportTicketController@addAction');
    Route::get('support/ticket/{id}/eta/{date}', 'Support\SupportTicketController@updateETA');
    Route::get('support/ticket/{id}/status/{status}', 'Support\SupportTicketController@updateStatus');
    Route::get('support/ticket/{id}/hours/{hours}', 'Support\SupportTicketController@updateHours');
    Route::get('support/ticket/{id}/priority/{priority}', 'Support\SupportTicketController@updatePriority');
    Route::resource('support/ticket', 'Support\SupportTicketController');


    // Comms
    Route::get('todo/dt/todo', 'Comms\TodoController@getTodo');
    Route::get('todo/create/{type}/{type_id}', 'Comms\TodoController@createType');
    Route::resource('todo', 'Comms\TodoController');
    Route::get('comms/notify/dt/notify', 'Comms\NotifyController@getNotify');
    Route::resource('comms/notify', 'Comms\NotifyController');
    Route::get('safety/tip/active', 'Comms\TipController@getActive');
    Route::resource('safety/tip', 'Comms\TipController');

    // PDF
    Route::get('pdf/test', 'Misc\PdfController@test');
    Route::get('pdf/workmethod/{id}', 'Misc\PdfController@workmethod');
    Route::get('pdf/planner/site/{site_id}/{date}/{weeks}', 'Misc\PdfController@plannerSite');

});

// Cron
Route::get('cron/nightly', 'Misc\CronController@nightly');
Route::get('cron/roster', 'Misc\CronController@roster');
Route::get('cron/qa', 'Misc\CronController@qa');
Route::get('cron/overdue-todo', 'Misc\CronController@overdueToDo');
Route::get('cron/expired-companydoc', 'Misc\CronController@expiredCompanyDoc');
Route::get('cron/expired-swms', 'Misc\CronController@expiredSWMS');

Route::get('test/cal', 'Misc\PagesController@testcal');
Route::get('manage/updateroles', 'Misc\PagesController@updateRoles');



