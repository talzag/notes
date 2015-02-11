<?php

class NotesController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	
	public function note() {
		if(Input::get("note") !== null) {
			// HACK ALERT
			// Pass back the note but with <br>'s changed to line breaks \n's
			$note = preg_replace('#<br\s*/?>#i', "\n", Note::find(Input::get("note"))->note);
			Log::info($note);
			return View::make("note")->with("note",$note);
		} else {
			return View::make("note");
		}
	}

	public function create()
	{
		$note = new Note;
		$note->note = nl2br(Input::get("note"));
		// this needs to chekc if user exists, if not great a temp one and store the session in the browser somehow
		if(Auth::check()) {
			$note->user_id = Auth::user()->id;
			$note->save();
			Log::info($note->id);
			return Response::json(array('success' => true, 'insert_id' => $note->id), 200);
		} else {
			return Response::json(array('success' => false, 'insert_id' => null), 201);
		}
	}

	public function data() {
		$notes = Note::where('user_id', Auth::user()->id)->get();
		$notes_return = array();
		foreach($notes as $note) {
			// format date created
	        $date = new DateTime($note->created_at, new DateTimeZone('UTC'));
			$date->setTimezone(new DateTimeZone('EST'));
			$formatted_date = $date->format('M j, Y g:i:s a');
			// format date updated
	        $date = new DateTime($note->updated_at, new DateTimeZone('UTC'));
			$date->setTimezone(new DateTimeZone('EST'));
			$formatted_date_updated = $date->format('M j, Y g:i:s a');
			$Parsedown = new Parsedown();
			$parsed_note = $Parsedown->text($note->note);
            array_push($notes_return, array(
                "date_created"=>array("date"=>$formatted_date,"id"=>$note->id),
                "date_updated"=>$formatted_date_updated,
				"note"=>$parsed_note,
				"note_raw"=>$note->note
            ));
		}
		// json encode response so it's usable
		$json = json_encode(array("data" => $notes_return));
		return $json;
	}

	public function edit() {
		Log::info(Input::get("id"));
		$id = Input::get("id");
		$note = Note::find($id);
		$note->note = Input::get("note");
		Log::info(nl2br(Input::get("note")));
		$note->save();
		return "success";
	}

	public function delete() {
		Log::info(Input::get("id"));
		$id = Input::get("id");
		$note = Note::find($id);
		$note->delete();
		return "success";
	}
}
