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

use Illuminate\Support\Facades\Route;

Route::get('test', 'TestController@index');

Route::get('export/Adr', 'Warehouse\ExportController@AdrExportPDF')->name("export.AdrExportPDF");
Route::get('export/withdrawForm', 'Warehouse\ExportController@formExportPDF')->name("export.formExportPDF");
Route::get('export/Bap', 'Warehouse\ExportController@exportPDF')->name("export.exportPDF");
Route::get('/policy', function() {
    return view('policy');
})->name('home.policy');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('auth.logout');

//Route::get('/', 'HomeController@index')->name('home.dashboard');
Route::get('/', 'DashboardController@index')->name('home.dashboard');
Route::get('/dashboard/juragan', 'DashboardController@totalJuragan')->name('home.juragan');
Route::get('/dashboard/outlet', 'DashboardController@totalOutlet')->name('home.outlet');
Route::get('/dashboard/cabinet', 'DashboardController@totalCabinet')->name('home.cabinet');
Route::get('/auditor/getAllLocationData', 'DashboardController@getAllLocationsData')->name('auditor.getLocations');

//Route::get('/home', 'HomeController@index')->name('home.index');
Route::get('/home', 'DashboardController@index')->name('home.index');


/*
 * Location Routes
 */
Route::get('/geo/province', 'Master\GeoController@province')->name('geo.province');
Route::get('/geo/city', 'Master\GeoController@city')->name('geo.city');
Route::get('/geo/district', 'Master\GeoController@district')->name('geo.district');
Route::get('/geo/village', 'Master\GeoController@village')->name('geo.village');
Route::get('/geo/driver', 'Master\GeoController@driver')->name('geo.driver');
Route::get('/geo/vehicle', 'Master\GeoController@vehicle')->name('geo.vehicle');
Route::get('/geo/outlet', 'Master\GeoController@outlet')->name('geo.outlet');
Route::get('/geo/outlet-retraction', 'Master\GeoController@outletRetraction')->name('geo.outletRetraction');
Route::get('/geo/route_plan', 'Master\GeoController@route_plan')->name('geo.route_plan');
Route::get('/geo/all-outlet', 'Master\GeoController@allOutlet')->name('geo.allOutlet');


/*
 * User Management Routes
 */
Route::resource('role', 'UserManagement\RoleController');
Route::get('role/{id}/permissions', 'UserManagement\RoleController@permissions')->name('role.permissions');
Route::get('role/{id}/addpermissions', 'UserManagement\RoleController@addpermissions')->name('role.addpermissions');
Route::post('role/{id}/addpermissions', 'UserManagement\RoleController@storepermissions')->name('role.storepermissions');
Route::delete('role/{id}/addservicepage', 'UserManagement\RoleController@destroypermissions')->name('role.destroypermissions');


Route::resource('permission', 'UserManagement\PermissionController');
Route::get('permission/{id}/servicepages', 'UserManagement\PermissionController@servicepages')->name('permission.servicepages');
Route::get('permission/{id}/addservicepage', 'UserManagement\PermissionController@addservicepage')->name('permission.addservicepage');
Route::post('permission/{id}/addservicepage', 'UserManagement\PermissionController@storeservicepage')->name('permission.storeservicepage');
Route::delete('permission/{id}/addservicepage', 'UserManagement\PermissionController@destroyservicepage')->name('permission.destroyservicepage');


Route::resource('user', 'UserManagement\UserController');
Route::get('/profile', 'UserManagement\UserController@profile')->name("user.profile");
Route::post('/profile', 'UserManagement\UserController@updateprofile')->name("user.updateprofile");
Route::get('user/{id}/resetpassword', 'UserManagement\UserController@resetpassword')->name("user.resetpassword");
Route::post('user/{id}/resetpassword', 'UserManagement\UserController@doresetpassword')->name("user.doresetpassword");


Route::resource('menu', 'UserManagement\MenuController');

Route::resource('submenu', 'UserManagement\SubMenuController');


/*
 * Juragan Routes
 */
Route::get('juragan/list', 'Master\JuraganController@Get')->name("juragan.list");
Route::get('juragan/export', 'JuraganManagement\JuraganController@export')->name("juragan.export");
Route::resource('juragan', 'JuraganManagement\JuraganController');
Route::get('jrg_dashboard', 'JuraganManagement\JuraganController@summary')->name("juragan.dashboard");
Route::get('/import/juragan', 'JuraganManagement\JuraganController@importForm')->name("juragan.import_form");
Route::post('/import/juragan', 'JuraganManagement\JuraganController@doImport')->name("juragan.import");
Route::get('juragan/{id}/resetpassword', 'JuraganManagement\JuraganController@resetpassword')->name("juragan.resetpassword");
Route::post('juragan/{id}/resetpassword', 'JuraganManagement\JuraganController@doresetpassword')->name("juragan.doresetpassword");
Route::get('/jrg_dashboard/outlet', 'Master\JuraganController@summaryOutlet')->name("juragan.dashboard_outlet");
Route::get('/jrg_dashboard/outlet_mandiri', 'Master\JuraganController@summaryOutletMandiri')->name("juragan.dashboard_outlet_mandiri");
Route::get('/jrg_dashboard/outlet_progress', 'Master\JuraganController@summaryOutletProgress')->name("juragan.dashboard_outlet_progress");
Route::get('/jrg_dashboard/juragan', 'Master\JuraganController@summaryJuragan')->name("juragan.dashboard_juragan");
Route::get('/jrg_dashboard/status_outlet', 'Master\JuraganController@statusOutletSummary')->name("juragan.dashboard_status_outlet");
Route::get('/jrg_dashboard/export_status_outlet', 'Master\JuraganController@ExportStatusOutletSummary')->name("juragan.export_status_outlet");
Route::get('/jrg/status_outlet', 'JuraganManagement\JuraganController@StatusApprovalOutlet')->name("juragan.status_approval_outlet");
Route::get('/jrg/export-validasi-toko', 'JuraganManagement\JuraganController@exportValidasiToko')->name("juragan.export-validasi-toko");
Route::get('/jrg/export-kirim-mandiri', 'JuraganManagement\JuraganController@exportTambahMandiri')->name("juragan.export-kirim-mandiri");

/*
 * Outlet Routes
 */
Route::name('outlet.')->group(function () {
    Route::get('outlet/get_outlet', 'Master\OutletController@GetOutlet')->name("ajax.outlet");
});
Route::get('outlet/export', 'OutletManagement\OutletController@export')->name("outlet.export");
Route::resource('outlet', 'OutletManagement\OutletController');
Route::get('/import/outlet', 'OutletManagement\OutletController@importForm')->name("outlet.import_form");
Route::post('/import/outlet', 'OutletManagement\OutletController@doImport')->name("outlet.import");
Route::post('outlet/doimport-csdp', 'OutletManagement\OutletController@doImportCSDP')->name('outlet.doImport-csdp');

/*
 * Driver Routes
 */
Route::get('driver/export', 'Driver\DriverController@export')->name("driver.export");
Route::resource('driver', 'Driver\DriverController');
Route::get('/import/driver', 'Driver\DriverController@importForm')->name("driver.import_form");
Route::post('/import/driver', 'Driver\DriverController@doImport')->name("driver.import");

/*
 * Vehicle Routes
 */
Route::get('vehicle/export', 'Driver\VehicleController@export')->name("vehicle.export");
Route::resource('vehicle', 'Driver\VehicleController');
Route::get('/import/vehicle', 'Driver\VehicleController@importForm')->name("vehicle.import_form");
Route::post('/import/vehicle', 'Driver\VehicleController@doImport')->name("vehicle.import");

/*
 * Hunter Routes
 */
Route::get('hunter/list', 'Master\HunterController@Get')->name("hunter.list");
Route::get('hunter/export', 'Hunter\HunterController@export')->name("hunter.export");
Route::get('hunter/export-survey', 'Hunter\HunterController@exportSurvey')->name("hunter.export-survey");
Route::get('hunter/dailymonitoring', 'Hunter\DailyMonitoringController@index')->name("dailymonitoring.index");
Route::get('hunter/dailymonitoring/export', 'Hunter\DailyMonitoringController@export')->name("dailymonitoring.export");
Route::get('hunter/performance', 'Hunter\PerformanceController@index')->name("performance.index");
Route::get('hunter/import', 'Hunter\HunterController@import')->name("hunter.import");
Route::post('hunter/doimport', 'Hunter\HunterController@doImport')->name("hunter.doImport");
Route::resource('hunter', 'Hunter\HunterController');
Route::resource('journeyplan', 'Hunter\JourneyPlanController');

/*
 * Cabinet Routes
 */
Route::get('cabinet/export', 'Warehouse\CabinetController@export')->name('cabinet.export');
Route::resource('cabinet', 'Warehouse\CabinetController');
Route::get('/geo/cabinet', 'Master\GeoController@cabinet')->name('geo.cabinet');
Route::get('/geo/cabinet-by-outlet', 'Master\GeoController@cabinetByOutlet')->name('geo.cabinetByOutlet');

/*
 * Route Plan Routes
 */
Route::get('route_plan/exportActivity', 'Warehouse\RoutePlanController@exportActivity')->name('route_plan.exportActivity');
Route::get('route_plan/cancel/{id}', 'Warehouse\RoutePlanController@cancelRoutePlan')->name('route_plan.cancel');
Route::resource('route_plan', 'Warehouse\RoutePlanController');

/*
 * Delivery Routes
 */
Route::get('delivery/exportPDF', 'Driver\DeliveryController@exportPDF')->name("delivery.exportPDF");
Route::get('delivery/export-activity', 'Driver\DeliveryController@exportActivity')->name("delivery.export-activity");
Route::resource('delivery', 'Driver\DeliveryController');
Route::get('delivery/{delivery}/edit-{type}', 'Driver\DeliveryController@edit')->name('delivery.edit')->where('type', 'deploy|tarik|tukar');
Route::put('delivery/{delivery}/update-{type}', 'Driver\DeliveryController@update')->name('delivery.update')->where('type', 'deploy|tarik|tukar');
Route::get('delivery/cancel/{id}', 'Driver\DeliveryController@cancelJourneyPlan')->name('journey_plan.cancel');

/*
 * Unilever Routes
 */
Route::get('unilever', 'Unilever\UnileverController@index')->name('unilever.index');
Route::get('unilever/export-outlet', 'Unilever\UnileverController@exportOutlet')->name('unilever.export-outlet');
Route::get('unilever/export-csdp', 'Unilever\UnileverController@exportCSDP')->name('outlet.export-csdp');
Route::get('unilever/import-outlet', 'Unilever\UnileverController@importOutlet')->name("unilever.import-outlet");
Route::post('unilever/doimport-outlet', 'Unilever\UnileverController@doImportOutlet')->name("unilever.doImport-outlet");
Route::get('unilever/{unilever}', 'Unilever\UnileverController@show')->name('unilever.show')->where('unilever', '[0-9]+');
Route::put('unilever/approve/{unilever}', 'Unilever\UnileverController@approve')->name('unilever.approve');
Route::put('unilever/reject/{unilever}', 'Unilever\UnileverController@reject')->name('unilever.reject');
Route::put('unilever/change-delivery-date/{unilever}', 'Unilever\UnileverController@changeDeliveryDate')->name('unilever.changeDeliveryDate');
Route::put('unilever/approve-bulk', 'Unilever\UnileverController@approveBulk')->name('unilever.approve-bulk');
Route::put('unilever/reject-bulk', 'Unilever\UnileverController@rejectBulk')->name('unilever.reject-bulk');
Route::get('unilever/pull-cabinet', 'Unilever\UnileverPullCabinetController@index')->name('unilever.pull-cabinet.index');
Route::get('unilever/pull-cabinet/export-outlet', 'Unilever\UnileverPullCabinetController@exportOutlet')->name('unilever.pull-cabinet.export-outlet');
Route::get('unilever/pull-cabinet/{id}', 'Unilever\UnileverPullCabinetController@show')->name('unilever.pull-cabinet.show');
Route::put('unilever/pull-cabinet/approve/{unilever}', 'Unilever\UnileverPullCabinetController@approve')->name('unilever.pull-cabinet.approve');
Route::put('unilever/pull-cabinet/reject/{unilever}', 'Unilever\UnileverPullCabinetController@reject')->name('unilever.pull-cabinet.reject');
Route::put('unilever/pull-cabinet/change-delivery-date/{unilever}', 'Unilever\UnileverPullCabinetController@changeDeliveryDate')->name('unilever.pull-cabinet.change-delivery-date');
Route::post('unilever/pull-cabinet/transform/{unilever}', 'Unilever\UnileverPullCabinetController@transform')->name('unilever.pull-cabinet.transform');
Route::put('unilever/pull-cabinet/approve-bulk', 'Unilever\UnileverPullCabinetController@approveBulk')->name('unilever.pull-cabinet.approve-bulk');
Route::put('unilever/pull-cabinet/reject-bulk', 'Unilever\UnileverPullCabinetController@rejectBulk')->name('unilever.pull-cabinet.reject-bulk');

/*
 * Answer Image Routes
 */
Route::get('answer-image/picture/{id}', 'Master\AnswerImageController@picture')->name('answerimage.picture')->middleware('auth');
Route::get('export-image/{type}/{id}', 'Master\AnswerImageController@get')->name('image.get')->middleware('auth');

/*
 * Auditor Routes
 */

Route::get('auditor/pull-cabinet', 'Auditor\PullCabinetController@index')->name('auditor.pull-cabinet.index');
Route::get('auditor/pull-cabinet/show/{id}', 'Auditor\PullCabinetController@show')->name('auditor.pull-cabinet.show');
Route::get('auditor/pull-cabinet/edit/{id}', 'Auditor\PullCabinetController@edit')->name('auditor.pull-cabinet.edit');
Route::put('auditor/pull-cabinet/{id}', 'Auditor\PullCabinetController@update')->name('auditor.pull-cabinet.update');
Route::delete('auditor/pull-cabinet/{id}', 'Auditor\PullCabinetController@destroy')->name('auditor.pull-cabinet.destroy');
Route::get('auditor/pull-cabinet/get-juragan',  'Auditor\PullCabinetController@getJuragan')->name('auditor.pull-cabinet.get-juragan');

Route::name('auditor.')->group(function () {
    Route::get('auditor/list', 'Master\AuditorController@Get')->name("ajax.list");
    Route::get('auditor/journeyplan_export', 'Auditor\JourneyPlanController@export')->name("journeyplan.export");
    Route::resource('auditor/journeyplan', 'Auditor\JourneyPlanController');
    Route::post('auditor/import', 'Auditor\JourneyPlanController@doImport')->name('journeyplan.import');
    Route::post('auditor-management/import', 'Auditor\AuditorController@doImport')->name('auditor.import');
    //Route::get('auditor/dailymonitoring', 'Auditor\DailyMonitoringController@index')->name("dailymonitoring.index");
    //Route::get('auditor/dailymonitoring/export', 'Auditor\DailyMonitoringController@export')->name("dailymonitoring.export");
});
Route::get('auditor/export', 'Auditor\AuditorController@export')->name("auditor.export");
Route::resource('auditor', 'Auditor\AuditorController');

Route::get('pull_self/AdrExportPDF', 'Warehouse\Transaction\MandiriController@AdrExportPDF')->name("pull_self.AdrExportPDF");
Route::get('pull_self/withdrawFormPDF', 'Warehouse\Transaction\MandiriController@formExportPDF')->name("pull_self.formExportPDF");
Route::get('pull_self/exportPDF', 'Warehouse\Transaction\MandiriController@exportPDF')->name("pull_self.exportPDF");
Route::get('pull_self/exportTukarMandiri', 'Warehouse\Transaction\MandiriController@export')->name("pull_self.exportTukarMandiri");
Route::get('pull_self/exportActivity', 'Warehouse\Transaction\MandiriController@exportActivity')->name("pull_self.exportActivity");
Route::post('pull_self/status/{id}', 'Warehouse\Transaction\MandiriController@status')->name("pull_self.status");
Route::resource('pull_self', 'Warehouse\Transaction\MandiriController');

Route::get('auditor/audit-plan/outlet', 'Auditor\AuditPlanController@outlet')->name('auditor.audit-plan.outlet');
Route::get('auditor/audit-plan/{id}', 'Auditor\AuditPlanController@index')->name('auditor.audit-plan.index');
Route::post('auditor/audit-plan', 'Auditor\AuditPlanController@store')->name('auditor.audit-plan.store');
Route::put('auditor/audit-plan/{id}', 'Auditor\AuditPlanController@update')->name('auditor.audit-plan.update');
Route::delete('auditor/audit-plan/{id}', 'Auditor\AuditPlanController@destroy')->name('auditor.audit-plan.destroy');
Route::delete('auditor/audit-plan/{id}/delete-all', 'Auditor\AuditPlanController@destroyAll')->name('auditor.audit-plan.destroy-all');

/*
 * Redermarkasi & Edit Data Kabinet Routes Routes
 */
Route::get('redermarkasi-edit-data-kabinet/redermarkasi/',  'Warehouse\Transaction\RedermarkasiController@index')->name('redermarkasi.index');
Route::get('redermarkasi-edit-data-kabinet/redermarkasi/create',  'Warehouse\Transaction\RedermarkasiController@create')->name('redermarkasi.create');
Route::get('redermarkasi-edit-data-kabinet/redermarkasi/edit/{id}',  'Warehouse\Transaction\RedermarkasiController@edit')->name('redermarkasi.edit');
Route::post('redermarkasi-edit-data-kabinet/redermarkasi/store',  'Warehouse\Transaction\RedermarkasiController@store')->name('redermarkasi.store');
Route::post('redermarkasi-edit-data-kabinet/redermarkasi/draft',  'Warehouse\Transaction\RedermarkasiController@draft')->name('redermarkasi.draft');
Route::post('redermarkasi-edit-data-kabinet/redermarkasi/update',  'Warehouse\Transaction\RedermarkasiController@update')->name('redermarkasi.update');
Route::get('redermarkasi-edit-data-kabinet/get-outlet-create/{id_juragan_asal}',  'Warehouse\Transaction\RedermarkasiController@getOutletCreate')->name('redermarkasi.getOutletCreate');
Route::get('redermarkasi-edit-data-kabinet/get-outlet-edit/{id_juragan_asal}',  'Warehouse\Transaction\RedermarkasiController@getOutletEdit')->name('redermarkasi.getOutletEdit');
Route::get('redermarkasi-edit-data-kabinet/redermarkasi/show/{id}',  'Warehouse\Transaction\RedermarkasiController@show')->name('redermarkasi.show');
Route::get('redermarkasi-edit-data-kabinet/redermarkasi/approve/{id}',  'Warehouse\Transaction\RedermarkasiController@approve')->name('redermarkasi.approve');
Route::post('redermarkasi-edit-data-kabinet/redermarkasi/reject',  'Warehouse\Transaction\RedermarkasiController@reject')->name('redermarkasi.reject');
Route::get('redermarkasi-edit-data-kabinet/edit-data-kabinet/',  'Transaction\RoutePlanController@cancelRoutePlan')->name('data-kabinet.index');

/*
 * Warehouse Management Routes
 */
Route::get('warehouse-management/',  'Warehouse\WarehouseManagementController@index')->name('warehouse_management.index');
Route::get('warehouse-management/get-admin-create',  'Warehouse\WarehouseManagementController@getAdminCreate')->name('warehouse_management.getAdminCreate');
Route::get('warehouse-management/get-admin-edit',  'Warehouse\WarehouseManagementController@getAdminEdit')->name('warehouse_management.getAdminEdit');
Route::get('warehouse-management/create',  'Warehouse\WarehouseManagementController@create')->name('warehouse_management.create');
Route::post('warehouse-management/store',  'Warehouse\WarehouseManagementController@store')->name('warehouse_management.store');
Route::post('warehouse-management/update',  'Warehouse\WarehouseManagementController@update')->name('warehouse_management.update');
Route::get('warehouse-management/edit/{id}',  'Warehouse\WarehouseManagementController@edit')->name('warehouse_management.edit');
Route::get('warehouse-management/show/{id}',  'Warehouse\WarehouseManagementController@show')->name('warehouse_management.show');
Route::get('warehouse-management/delete/{id}',  'Warehouse\WarehouseManagementController@delete')->name('warehouse_management.delete');

/*
 * Juragan to Warehouse Management Routes
 */
Route::get('juragan-to-warehouse/',  'WarehouseMapping\JuraganToWarehouseManagementController@index')->name('juragan_to_warehouse.index');
Route::get('juragan-to-warehouse/get-warehouse-name/{id}',  'WarehouseMapping\JuraganToWarehouseManagementController@getWarehouseName')->name('juragan_to_warehouse.getWarehouseName');
Route::get('juragan-to-warehouse/get-juragan',  'WarehouseMapping\JuraganToWarehouseManagementController@getJuragan')->name('juragan_to_warehouse.getJuragan');
Route::get('juragan-to-warehouse/create',  'WarehouseMapping\JuraganToWarehouseManagementController@create')->name('juragan_to_warehouse.create');
Route::get('juragan-to-warehouse/edit/{id}',  'WarehouseMapping\JuraganToWarehouseManagementController@edit')->name('juragan_to_warehouse.edit');
Route::get('juragan-to-warehouse/show/{id}',  'WarehouseMapping\JuraganToWarehouseManagementController@show')->name('juragan_to_warehouse.show');
Route::get('juragan-to-warehouse/delete/{id}',  'WarehouseMapping\JuraganToWarehouseManagementController@delete')->name('juragan_to_warehouse.delete');
Route::post('juragan-to-warehouse/store',  'WarehouseMapping\JuraganToWarehouseManagementController@store')->name('juragan_to_warehouse.store');
Route::post('juragan-to-warehouse/update',  'WarehouseMapping\JuraganToWarehouseManagementController@update')->name('juragan_to_warehouse.update');

/*
 * Vehicle to Warehouse Management Routes
 */
Route::get('vehicle-to-warehouse/',  'WarehouseMapping\VehicleToWarehouseManagementController@index')->name('vehicle_to_warehouse.index');
Route::get('vehicle-to-warehouse/get-warehouse-name/{id}',  'WarehouseMapping\VehicleToWarehouseManagementController@getWarehouseName')->name('vehicle_to_warehouse.getWarehouseName');
Route::get('vehicle-to-warehouse/get-vehicle',  'WarehouseMapping\VehicleToWarehouseManagementController@getVehicle')->name('vehicle_to_warehouse.getVehicle');
Route::get('vehicle-to-warehouse/create',  'WarehouseMapping\VehicleToWarehouseManagementController@create')->name('vehicle_to_warehouse.create');
Route::get('vehicle-to-warehouse/edit/{id}',  'WarehouseMapping\VehicleToWarehouseManagementController@edit')->name('vehicle_to_warehouse.edit');
Route::get('vehicle-to-warehouse/show/{id}',  'WarehouseMapping\VehicleToWarehouseManagementController@show')->name('vehicle_to_warehouse.show');
Route::get('vehicle-to-warehouse/delete/{id}',  'WarehouseMapping\VehicleToWarehouseManagementController@delete')->name('vehicle_to_warehouse.delete');
Route::post('vehicle-to-warehouse/store',  'WarehouseMapping\VehicleToWarehouseManagementController@store')->name('vehicle_to_warehouse.store');
Route::post('vehicle-to-warehouse/update',  'WarehouseMapping\VehicleToWarehouseManagementController@update')->name('vehicle_to_warehouse.update');

/*
 * Driver to Warehouse Management Routes
 */
Route::get('driver-to-warehouse/',  'WarehouseMapping\DriverToWarehouseManagementController@index')->name('driver_to_warehouse.index');
Route::get('driver-to-warehouse/get-warehouse-name/{id}',  'WarehouseMapping\DriverToWarehouseManagementController@getWarehouseName')->name('driver_to_warehouse.getWarehouseName');
Route::get('driver-to-warehouse/get-driver',  'WarehouseMapping\DriverToWarehouseManagementController@getDriver')->name('driver_to_warehouse.getDriver');
Route::get('driver-to-warehouse/create',  'WarehouseMapping\DriverToWarehouseManagementController@create')->name('driver_to_warehouse.create');
Route::get('driver-to-warehouse/edit/{id}',  'WarehouseMapping\DriverToWarehouseManagementController@edit')->name('driver_to_warehouse.edit');
Route::get('driver-to-warehouse/show/{id}',  'WarehouseMapping\DriverToWarehouseManagementController@show')->name('driver_to_warehouse.show');
Route::get('driver-to-warehouse/delete/{id}',  'WarehouseMapping\DriverToWarehouseManagementController@delete')->name('driver_to_warehouse.delete');
Route::post('driver-to-warehouse/store',  'WarehouseMapping\DriverToWarehouseManagementController@store')->name('driver_to_warehouse.store');
Route::post('driver-to-warehouse/update',  'WarehouseMapping\DriverToWarehouseManagementController@update')->name('driver_to_warehouse.update');

/*
 * Call Center Routes
 */
Route::get('call-center/pull-cabinet', 'CallCenter\PullCabinetController@index')->name('callcenter.pull-cabinet.index');
Route::get('call-center/pull-cabinet/export-outlet', 'CallCenter\PullCabinetController@exportOutlet')->name('callcenter.pull-cabinet.export-outlet');
Route::get('call-center/pull-cabinet/{id}', 'CallCenter\PullCabinetController@show')->name('callcenter.pull-cabinet.show');
Route::put('call-center/pull-cabinet/{id}', 'CallCenter\PullCabinetController@update')->name('callcenter.pull-cabinet.update');
Route::put('call-center/pull-cabinet/approve/{id}', 'CallCenter\PullCabinetController@approve')->name('callcenter.pull-cabinet.approve');
Route::put('call-center/pull-cabinet/cancel/{id}', 'CallCenter\PullCabinetController@cancel')->name('callcenter.pull-cabinet.cancel');
Route::put('call-center/pull-cabinet/postpone/{id}', 'CallCenter\PullCabinetController@postpone')->name('callcenter.pull-cabinet.postpone');

/*
 * Warehouse Route Plan Tarik Kabinet Routes
 */
Route::get('warehouse/route-plan-pull-cabinet', 'Warehouse\RoutePlanPullCabinetController@index')->name('warehouse.route-plan-pull-cabinet.index');
Route::get('warehouse/route-plan-pull-cabinet/export', 'Warehouse\RoutePlanPullCabinetController@export')->name('warehouse.route-plan-pull-cabinet.export');
Route::get('warehouse/route-plan-pull-cabinet/create', 'Warehouse\RoutePlanPullCabinetController@create')->name('warehouse.route-plan-pull-cabinet.create');
Route::get('warehouse/route-plan-pull-cabinet/{id}', 'Warehouse\RoutePlanPullCabinetController@show')->name('warehouse.route-plan-pull-cabinet.show');
Route::post('warehouse/route-plan-pull-cabinet', 'Warehouse\RoutePlanPullCabinetController@store')->name('warehouse.route-plan-pull-cabinet.store');
Route::put('warehouse/route-plan-pull-cabinet/{id}', 'Warehouse\RoutePlanPullCabinetController@update')->name('warehouse.route-plan-pull-cabinet.update');
Route::put('warehouse/route-plan-pull-cabinet/cancel/{id}', 'Warehouse\RoutePlanPullCabinetController@cancel')->name('warehouse.route-plan-pull-cabinet.cancel');
Route::get('warehouse/route-plan-pull-cabinet/edit/{id}', 'Warehouse\RoutePlanPullCabinetController@edit')->name('warehouse.route-plan-pull-cabinet.edit');
Route::delete('warehouse/route-plan-pull-cabinet/{id}', 'Warehouse\RoutePlanPullCabinetController@destroy')->name('warehouse.route-plan-pull-cabinet.destroy');
Route::get('warehouse/route-plan-pull-cabinet/download-art/{id}', 'Warehouse\RoutePlanPullCabinetController@downloadArt')->name('warehouse.route-plan-pull-cabinet.download-art');
