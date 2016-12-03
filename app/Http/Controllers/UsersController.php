<?php namespace App\Http\Controllers;

use Log;
use Auth;
use User;
use Request;
use Input;
use Hash;
use Route;
use Note;
use Response;

class UsersController extends Controller {

	// create a new user
	public function create_user() {
    	if(Auth::user() && Auth::user()->is_temporary == 1) {
        	// if the current user is a temporary one, make a new permanent user and migrate notes
        	return $this->migrate_from_guest();
    	} else if(!Auth::user()) {
        	// if there is no user logged in, create a new one
            return $this->create_permanent_user();
    	} else {
        	// if there is a logged in user that is permanent, this shouldn't have happened
        	Log::error("Trying to make a new user when a permanent user is logged in. Shouldn't be possible");
    	}
    }

    public function create_permanent_user() {
		// create a new user
		User::create([
			"email" => Input::get("email"),
			"password" => Hash::make(Input::get("password"))
		]);
		// log that user in
		Auth::attempt(array(
			"email" => Input::get("email"),
			"password" => Input::get("password")
		));
		// if they're logged in, add their new note and redirect to their all notes page
		if(Auth::check()) {
			$params = array(
				"note_text"=>Input::get("note_text")
			);
			$request = Request::create('notes/create', 'POST',$params);
			Request::replace($request->input());
			return Route::dispatch($request)->getContent();
		} else {
			return "failed";
		}
    }

	public function create_guest() {
		$start_email = "";
		$email= $start_email.Hash::make(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,30))."@testuser.com";
		User::create([
			"email" => $email,
			"password" => Hash::make("tempPasswordAlwaysTheSame!"),
			"is_temporary" => 1
		]);
		// log that user in
		Auth::attempt(array(
			"email" => $email,
			"password" => "tempPasswordAlwaysTheSame!"
		));
		// if they're logged in, add their new note and redirect to their all notes page
		if(Auth::check()) {
			$params = array(
				"note_text"=>Input::get("note_text")
			);
			$request = Request::create('notes/create', 'POST',$params);
			Request::replace($request->input());
			return Route::dispatch($request)->getContent();
		} else {
			return "failed";
		}
	}

	public function migrate_from_guest() {
		if(Auth::user()->is_temporary === 0) {
			Log::error("This is already a permanent user this shouldn't have happened");
		} else {
			$current_id = Auth::user()->id;
			User::create([
				"email" => Input::get("email"),
				"password" => Hash::make(Input::get("password"))
			]);
			// log that user in
			Auth::attempt(array(
				"email" => Input::get("email"),
				"password" => Input::get("password")
			));
			// if they're logged in, add their new note and redirect to their all notes page
			if(Auth::check()) {
			// move all the notes from the old user to the new user
				$notes = Note::where('user_id', $current_id)->get();
				foreach($notes as $note) {
					$save = Note::find($note->id);
					$save->user_id = Auth::user()->id;
					$save->save();
				}
				return Response::json(array('success' => true, 'insert_id' => null), 200);
			} else {
				return Response::json(array('success' => false, 'insert_id' => null), 200);
			}
		}
	}
}
