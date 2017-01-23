<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
    Route::get('/optimize', function () {
	  	Artisan::call('cache:clear');
	  	Artisan::call('clear-compiled');
	  	Artisan::call('config:cache');
	  	Artisan::call('view:clear');
	  	Artisan::call('optimize', ['--quiet' => true, '--force' => true]);
	    return 'Artisan Command Runs Successfully!'; 
	  });

    Route::group(['middleware'=>'guest'],function(){
	  Route::get('/', function () {
	    return view('login');
	  });
	  Route::get('/login', function () {
	    return view('login');
	  });
	});

    Route::group(['middleware'=>'ldap'],function(){
	 
	 Route::group(['middleware' => 'ACL'], function () {
        Route::resource('office', 'OfficeController');
		Route::resource('region', 'RegionController');
		Route::resource('requestType', 'RequestTypeController');
		Route::resource('containerType', 'ContainerTypeController');
		Route::resource('errorType', 'ErrorTypeController');
		Route::resource('priorityType', 'PriorityTypeController');
		Route::resource('errorCat', 'ErrorCatController');
		Route::resource('mode', 'ModeController');
		Route::resource('pricingArea', 'PricingAreaController');
		Route::resource('holiday', 'HolidayController');
		Route::resource('tat', 'TatController');
		Route::resource('rfi', 'RfiTypeController');
		Route::resource('status', 'StatusController');
		Route::resource('reminder', 'ReminderMailController');
		Route::resource('indexing', 'IndexingController');
        Route::resource('userAccess', 'UserAccessController');
        Route::resource('publishing', 'PublishingController');	
        Route::resource('auditing', 'AuditingController');	
        Route::get('adminTracker', 'PublishingController@getAdminTracker')->name('admin.tracker.list');	
        Route::get('/queue/rfi', 'RfiController@index')->name('queue.rfi');
        Route::get('/queue/completed', 'RfiController@completedQueue')->name('queue.completed');
        Route::get('/queue/followup','FollowUpController@index')->name('queue.followup');
        Route::get('/queue/pricer','FollowUpController@pricerData')->name('queue.pricer');
        Route::get('/queue/partnercode','FollowUpController@partnerCodeData')->name('queue.partnercode');
        Route::post('/pricer/store','FollowUpController@store')->name('pricer.store');
        Route::get('/report/daily', 'ReportController@dailyReport')->name('report.daily');
		Route::get('/report/weekly', 'ReportController@weeklyReport')->name('report.weekly');
		Route::get('/report/monthly', 'ReportController@monthlyReport')->name('report.monthly');
		Route::get('/report/capa', 'ReportController@capaReport')->name('report.capa');
        Route::get('/report/errors', 'ReportController@errorReport')->name('report.errors');
        Route::get('/report/rfiLog', 'ReportController@rfiLogReport')->name('report.rfiLog');
        Route::get('/report/productivity', 'ReportController@productivityReport')->name('report.productivity');
	});

     Route::post('/pricer/updateStatus','FollowUpController@updateStatus');
	 Route::get('/partnerData','PublishingController@partnerData');
	 Route::post('/requestNum','IndexingController@requestNumber');
	 Route::get('/ootEnable/{id}','PublishingController@ootEnable');
	 Route::get('/ootAuditEnable/{id}','AuditingController@ootEnable');
	 Route::get('/publishing/delete/{id}','PublishingController@destroy');
     Route::get('/publishing/changeStatus/{id}','PublishingController@changeStatus');
	 Route::get('/auditing/changeStatus/{id}','AuditingController@changeStatus');
	 Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
	 Route::resource('menu', 'MenuController');
	 Route::get('/auth/logout', 'LdapController@logout')->name('auth.logout');
});


Route::post('/auth/check', 'LdapController@checkLogin')->name('check.login');
Route::get('/tatcheck', 'IndexingController@tat');
Route::get('/mail/reminder', 'FollowUpController@sendReminder');
Route::get('/exception', function () {
    return view('errors.tokenError');
});

//Route::auth();
Route::get('/home', 'HomeController@index');

Route::get('/permission', 'MenuController@allPermissions');
Route::get('/permission/status/{id}/{flag}', 'MenuController@permissionStatus');
Route::get('/permission/sync', 'UserAccessController@syncRolePermission');

/*Route::filter('csrf', function($route, $request) {
    if (strtoupper($request -> getMethod()) === 'GET') {
        return;
        // get requests are not CSRF protected
    }

    $token = $request -> ajax() ? $request -> header('X-CSRF-Token') : Input::get('_token');

    if (Session::token() != $token) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});*/