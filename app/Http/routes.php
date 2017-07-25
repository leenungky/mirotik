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
// */

Route::get('/', [
    'as' => 'index', 'uses' => 'UserController@getLogin'
]);


// Route::get('/signin', [
//     'as' => 'signin', 'uses' => 'InventoryController@signin'
// ]);


Route::group(['middleware' => 'logic'], function(){
	Route::controller('/user', 'UserController');
	Route::controller('/role', 'RoleController');
	Route::controller('/cities', 'CityController');
	Route::controller('/agent', 'AgentController');
	Route::controller('/collect', 'CollectController');	
	Route::controller('/price', 'PriceController');	
	Route::controller('/employ', 'EmployeeController');		
	Route::controller('/tpl', 'TreeplController');		
});

Route::group(['prefix' => 'auth'], function(){
	Route::auth();
});

Route::group(['prefix' => 'customer', 'middleware' => 'logic'], function(){
	Route::any('/', ['as' => 'index_customer', 'uses' => 'CustomerController@index']);	
	Route::any('/new', ['as' => 'new_customer', 'uses' => 'CustomerController@newcustomer']);	
	Route::any('/create', ['as' => 'create_customer', 'uses' => 'CustomerController@create']);	
	Route::any('/edit/{id}', ['as' => 'edit', 'uses' => 'CustomerController@edit']);	
	Route::any('/update/{id}', ['as' => 'edit', 'uses' => 'CustomerController@update']);
	Route::any('/delete/{id}', ['as' => 'delete', 'uses' => 'CustomerController@delete']);	
});

Route::group(['prefix' => 'transaction', 'middleware' => 'logic'], function(){
	Route::any('/', ['as' => 'index_transaction', 'uses' => 'TransactionController@index']);	
	Route::any('/new', ['as' => 'new_transaction', 'uses' => 'TransactionController@newtrasaction']);	
	Route::any('/create', ['as' => 'create_transaction', 'uses' => 'TransactionController@create']);	
	Route::any('/createajax', ['as' => 'create_transaction', 'uses' => 'TransactionController@createajax']);	
	Route::any('/edit/{id}', ['as' => 'edit', 'uses' => 'TransactionController@edit']);	
	Route::any('/update/{id}', ['as' => 'edit', 'uses' => 'TransactionController@update']);
	Route::any('/delete/{id}', ['as' => 'delete', 'uses' => 'TransactionController@delete']);
	Route::any('/getcustomer', ['as' => 'getcustomer', 'uses' => 'TransactionController@getcustomer']);
	Route::any('/getkecamatan', ['as' => 'getkecamatan', 'uses' => 'TransactionController@getkecamatan']);
	Route::any('/newtotal', ['as' => 'newtotal', 'uses' => 'TransactionController@newtotal']);
	Route::any('/createtotal', ['as' => 'createtotal', 'uses' => 'TransactionController@createtotal']);	
	Route::any('/findtoprint', ['as' => 'qrcode', 'uses' => 'TransactionController@findtoprint']);	
	Route::any('/cancel', ['as' => 'qrcode', 'uses' => 'TransactionController@cancel']);	
	Route::any('/taken', ['as' => 'qrcode', 'uses' => 'TransactionController@taken']);	
	Route::any('/create_taken', ['as' => 'qrcode', 'uses' => 'TransactionController@create_taken']);
});


Route::group(['prefix' => 'report','middleware' => 'logic'], function(){
	Route::any('/biaya', ['as' => 'laporan_biaya', 'uses' => 'ReportController@biaya']);	
	Route::any('/biaya_pdf', ['as' => 'laporan_biaya_pdf', 'uses' => 'ReportController@biaya_pdf']);	
	Route::any('/pdfview', ['as' => 'laporan_biaya_pdf_view', 'uses' => 'ReportController@pdfview']);	
	Route::any('/biaya_excel', ['as' => 'laporan_biaya_excel', 'uses' => 'ReportController@biaya_excel']);	
	Route::any('/pengiriman', ['as' => 'laporan_pengiriman', 'uses' => 'ReportController@pengiriman']);			
	Route::any('/pengiriman_excel', ['as' => 'laporan_pengiriman', 'uses' => 'ReportController@pengiriman_excel']);	
});

Route::group(['prefix' => 'report','middleware' => 'logic'], function(){
	Route::any('/biaya_excel', ['as' => 'laporan_biaya_excel', 'uses' => 'ReportController@biaya_excel']);	
});
