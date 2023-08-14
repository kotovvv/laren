<?php

use App\Http\Controllers\API\v1\loginController;
use App\Http\Controllers\API\v1\ProvidersController;
use App\Http\Controllers\API\v1\UsersController;
use App\Http\Controllers\API\v1\LidsController;
use App\Http\Controllers\API\v1\LogsController;
use App\Http\Controllers\API\v1\StatusesController;
use App\Http\Controllers\API\v1\ImportsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [loginController::class, 'login']);

Route::resource('provider', ProvidersController::class);
Route::resource('statuses', StatusesController::class);
Route::resource('users', UsersController::class);
Route::resource('lids', LidsController::class);
Route::resource('imports', ImportsController::class);

Route::get('statusall', [StatusesController::class, 'getall'])->name('stasusall');
Route::get('providerall', [ProvidersController::class, 'getall'])->name('providerall');
Route::post('status_provider', [ProvidersController::class, 'status_provider'])->name('status_provider');
Route::post('status_users', [UsersController::class, 'status_users'])->name('status_users');

Route::get('getusers', [UsersController::class, 'getusers'])->name('getusers');
Route::post('getusers', [UsersController::class, 'getrelatedusers'])->name('getrelatedusers');
Route::get('users/getroles', [UsersController::class, 'getroless'])->name('user.getroles');
Route::post('user/update', [UsersController::class, 'update'])->name('user.update');
Route::get('userlids/{id}', [LidsController::class, 'userLids'])->name('userlids');
Route::post('getLidsPost', [LidsController::class, 'getLidsPost'])->name('getLidsPost');
Route::post('getLids3', [LidsController::class, 'getLids3'])->name('getLids3');
Route::post('statuslids', [LidsController::class, 'statusLids'])->name('statuslids');
Route::get('GetAllLeadsOnUserId', [LidsController::class, 'GetAllLeadsOnUserId'])->name('GetAllLeadsOnUserId');
Route::get('todaylids/{id}', [LidsController::class, 'todaylids'])->name('todaylids');
Route::delete('provider/{id}', [ProvidersController::class, 'destroy']);
Route::delete('user/{id}', [UsersController::class, 'deleteuser']);
Route::delete('delDataUser/{id}', [UsersController::class, 'delDataUser']);
Route::post('setBalans', [UsersController::class, 'setBalans']);
Route::get('getBalansMonth/{id}', [UsersController::class, 'getBalansMonth']);
Route::get('getStatusesMonth/{id}', [UsersController::class, 'getStatusesMonth']);
Route::get('getDepozitsMonth/{id}', [UsersController::class, 'getDepozitsMonth']);
Route::get('getCallsMonth/{id}', [UsersController::class, 'getCallsMonth']);
Route::get('getDataDay/{id}', [UsersController::class, 'getDataDay']);

Route::post('Lid/newlids', [LidsController::class, 'newlids'])->name('Lid.newlids');
Route::post('Lid/updatelids', [LidsController::class, 'updatelids'])->name('Lid.updatelids');
Route::post('Lid/searchlids', [LidsController::class, 'searchlids'])->name('Lid.searchlids');
Route::post('Lid/searchlids3', [LidsController::class, 'searchlids3'])->name('searchlids3');
Route::get('getLeadIdOnTelInfo', [LidsController::class, 'getLeadIdOnTelInfo'])->name('getLeadIdOnTelInfo');
Route::get('getLeadsIdBetweenDates', [LidsController::class, 'getLeadsIdBetweenDates'])->name('getLeadsIdBetweenDates');
Route::post('getLidsOnDate', [LidsController::class, 'getLidsOnDate'])->name('getLidsOnDate');
Route::get('getLeadOnId/{id}', [LidsController::class, 'getLeadOnId'])->name('getLeadOnId');
Route::get('importLeadInDb', [LidsController::class, 'importLeadInDb'])->name('importLeadInDb');
Route::post('Lid/changelidsuser', [LidsController::class, 'changelidsuser'])->name('Lid.changelidsuser');
Route::post('Lid/ontime', [LidsController::class, 'ontime'])->name('Lid.ontime');
Route::post('Lid/deletelids', [LidsController::class, 'deletelids'])->name('Lid.deletelids');
Route::post('log/add', [LogsController::class, 'add'])->name('log.add');
Route::post('log/tellog', [LogsController::class, 'tellog'])->name('log.tellog');
Route::get('getlogonid/{id}', [LogsController::class, 'getlogonid'])->name('getlogonid');
Route::get('StasusesOfId/{id}', [LogsController::class, 'StasusesOfId'])->name('StasusesOfId');
Route::get('importLead', [LidsController::class, 'importLead'])->name('importLead');
Route::post('importLeadPost', [LidsController::class, 'importLeadPost'])->name('importLeadPost');
Route::get('importLeadJs', [LidsController::class, 'importLeadJs'])->name('importLeadJs');
Route::get('importLeadDuplicate', [LidsController::class, 'importLeadDuplicate'])->name('importLeadDuplicate');
Route::get('getInfoLeadFtd', [LidsController::class, 'getInfoLeadFtd'])->name('getInfoLeadFtd');
Route::get('getLoadedLeads', [LidsController::class, 'getLoadedLeads'])->name('getLoadedLeads');
Route::get('getLeadsByPages', [LidsController::class, 'getLeadsByPages'])->name('getLeadsByPages');
Route::get('getLiadsOnDates', [LidsController::class, 'getLiadsOnDates'])->name('getLiadsOnDates');
Route::post('setDepozit', [LidsController::class, 'setDepozit'])->name('setDepozit');
Route::get('getHmLidsUser/{id}', [LidsController::class, 'getHmLidsUser'])->name('getHmLidsUser');
Route::get('getInfoLeadDeposit', [LidsController::class, 'getInfoLeadDeposit'])->name('getInfoLeadDeposit');
Route::get('GetAllProviderLeadsBetweenDates', [LidsController::class, 'GetAllProviderLeadsBetweenDates'])->name('GetAllProviderLeadsBetweenDates');
Route::post('getBTC', [LidsController::class, 'getBTC'])->name('getBTC');
Route::post('qtytel', [LidsController::class, 'qtytel'])->name('qtytel');

Route::get('historyLid/{id}', [ProvidersController::class, 'historyLid'])->name('historyLid');
Route::get('pieAll/{id}', [ProvidersController::class, 'pieAll'])->name('pieAll');
Route::get('pieTime/{id}/{start_day}/{stop_day}', [ProvidersController::class, 'pieTime'])->name('pieTime');
Route::get('getDataTime/{id}/{start_day}/{stop_day}', [ProvidersController::class, 'getDataTime'])->name('getDataTime');
Route::get('getOffices', [UsersController::class, 'getOffices']);
Route::post('office/update', [UsersController::class, 'updateOffice']);
Route::get('usersTree', [UsersController::class, 'usersTree']);
Route::get('getServers/{id}', [UsersController::class, 'getServers']);
Route::post('session', [loginController::class, 'session']);
Route::post('putBTC', [ImportsController::class, 'putBTC']);
Route::post('getBTCsOnDate', [ImportsController::class, 'getBTCsOnDate']);
Route::post('getBTCotherOnDate', [ImportsController::class, 'getBTCotherOnDate']);
Route::post('changeDateBTC', [LidsController::class, 'changeDateBTC']);
Route::post('getAssignedBTC', [LidsController::class, 'getAssignedBTC']);
Route::post('provider_importlid', [LidsController::class, 'provider_importlid']);
Route::post('checkEmails', [LidsController::class, 'checkEmails']);
Route::post('getlidsImportedProvider', [LidsController::class, 'getlidsImportedProvider']);
Route::get('onCdr', [LogsController::class, 'onCdr']);
Route::post('getCalls', [LogsController::class, 'getCalls']);