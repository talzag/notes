<?php

class UsersController extends BaseController {

	// create a new user
	public function create_user() {
		// create a new user
		Log::info(Input::all());
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
				"note"=>Input::get("note")
			);
			$request = Request::create('addnote', 'POST',$params);
			Request::replace($request->input());
			json_decode(Route::dispatch($request)->getContent());
			return "success";
		} else {
			return "failed";
		}
	}

	public function create_guest() {
		Log::info(Input::all());
		$start_email = "";
		$email= $start_email.Hash::make(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,30))."@gmail.com";
		Log::info("email ".$email);
		User::create([
			"email" => $email,
			"password" => Hash::make("tempPasswordAlwaysTheSame!"),
			"is_temp" => 1
		]);
		// log that user in
		Auth::attempt(array(
			"email" => $email,
			"password" => "tempPasswordAlwaysTheSame!"
		));
		// if they're logged in, add their new note and redirect to their all notes page
		if(Auth::check()) {
			$params = array(
				"note"=>Input::get("note")
			);
			$request = Request::create('addnote', 'POST',$params);
			Request::replace($request->input());
			json_decode(Route::dispatch($request)->getContent());
			return "success";
		} else {
			return "failed";
		}
	}

	public function migrate_from_guest() {
		if(Auth::user()->is_temporary === 1) {
			Log::info("We shall make a new permanent user to house this temporary user! HUZZAH!");
		} else {
			$current_id = Auth::user()->id;
			Log::info("This is already a permanent user this shouldn't have happened");
			Log::info(Input::all());
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
				Log::info($notes);
				foreach($notes as $note) {
					$save = Note::find($note->id);
					$save->user_id = Auth::user()->id;
					$save->save();
				}
				return "success";
			} else {
				return "failed";
			}
		}
	}
}
