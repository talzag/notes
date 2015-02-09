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

Route::get('/notes', function() {
	$notes = Note::all()->reverse();
    return View::make('allnotes')->with('notes', $notes);
})->before("auth");

Route::get("/",function() {
	// ready the request
	return View::make("note");
});

Route::get("/localtest",function() {
	Log::info(Input::all());
	Helper::isThisWorking();
});

Route::group(array('prefix' => 'users', 'before' => 'ajax'), function() {
    Route::post("create","UsersController@create_user");
    Route::post("guest","UsersController@create_guest");
    Route::post("migrate","UsersController@migrate_from_guest");
});

Route::get("login","SessionsController@create");
Route::get("logout","SessionsController@destroy");
Route::resource("sessions","SessionsController");

Route::post("addnote", "NotesController@newNote");

// data routes

Route::get("all_notes_data","NotesController@allNotes");
Route::post("note","NotesController@edit");
Route::delete("note","NotesController@delete");
