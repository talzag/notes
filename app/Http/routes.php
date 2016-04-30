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

/* Route Filters */

Route::filter('ajax', function() {
    if (!Request::ajax()) {
        App::abort(404);
    }
});


Route::get("/","NotesController@note");
Route::get("/realtime",function() {
    return View::make('realtime');
});


Route::group(array('prefix' => 'stats'), function() {
    Route::get('/', function() {
      return Redirect::to("/models/stats");
    });
    Route::group(array('before' => 'ajax'), function() {
        Route::get("models","StatsController@models");
        Route::get('model_stats', "StatsController@model_stats");
        Route::get("data", "StatsController@stats_data");
    });
});

Route::group(array('prefix' => 'notes'), function() {
    Route::get('/', function() {
        return View::make('allnotes');
    })->before('auth');

    Route::group(array('before' => 'ajax'), function() {
        Route::post("markdown","NotesController@convertMarkdownIntoHTML");
        Route::get("data", "NotesController@data");
        Route::post("create", "NotesController@save");
        Route::post("edit", "NotesController@edit");
        Route::post("publish", "NotesController@publish");
        Route::delete("archive", "NotesController@archive");
    });
});

Route::group(array('prefix' => 'archives'), function() {
    Route::get('/', function() {
        return View::make('archives');
    })->before('auth');

    Route::group(array('before' => 'ajax'), function() {
        Route::get("archives_data", "NotesController@archives");
        Route::post("create", "NotesController@save");
        Route::post("edit", "NotesController@edit");
        Route::post("restore", "NotesController@restore");
        Route::delete("delete", "NotesController@delete");
    });
});

Route::group(array('prefix' => 'users', 'before' => 'ajax'), function() {
    Route::post("create","UsersController@create_user");
    Route::post("guest","UsersController@create_guest");
    Route::post("migrate","UsersController@migrate_from_guest");
});

Route::get("login","SessionsController@create");
Route::get("logout","SessionsController@destroy");
Route::get("destroycookie",function() {
    $cookie = Cookie::forget('blankslatefirstime');
    return Redirect::to("/logout")->withCookie($cookie);
});
Route::resource("sessions","SessionsController");


Route::group(array('prefix' => 'google'), function() {
    Route::post('/addDoc', "GoogleController@addDoc");
    Route::get("/clearCookie",function() {
        Session::forget('upload_token');
        return Redirect::to("/");
    });
});

Route::group(array('prefix' => 'pdf'), function() {
    Route::match(array('GET', 'POST'), 'create', "PDFController@create");
});

Route::group(array('prefix' => 'admin','before' => array('auth|admin')), function() {
    Route::get('/users', "AdminController@downloadUsers");
});

Route::controller('password', 'RemindersController');
Route::post("forgotPassword","RemindersController@postRemind");


Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
