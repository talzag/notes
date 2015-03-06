<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/* Route Filters */

Route::filter('ajax', function() {
    if (!Request::ajax()) {
        App::abort(404);
    }
});

/* Routes */

Route::get("/","NotesController@note");


//stats
Route::group(array('prefix' => 'stats'), function() {
    Route::get('/', "StatsController@stats_page");
    Route::group(array('before' => 'ajax'), function() {
        Route::get("data", "StatsController@stats_data");
    });
});

Route::group(array('prefix' => 'notes'), function() {
    Route::get('/', function() {
        return View::make('allnotes');
    })->before('auth');

    Route::group(array('before' => 'ajax'), function() {
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

// Integration Routes

Route::group(array('prefix' => 'google'), function() {
    Route::get('/', "GoogleController@test");
    Route::get('/viewtest', "GoogleController@viewtest");
});
