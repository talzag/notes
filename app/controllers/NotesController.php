<?php

class NotesController extends BaseController {
    // the root "note" url logic. Either it's a new note or an existing note. 
	public function note() {
		if(Input::get("note") !== null) {
            // HACK ALERT Pass back the note but with <br>'s changed to line breaks \n's
			// if the note exists, check if it is viewable. If so, show the note, if not, don't show it. 
			if(!is_null(Note::find(Input::get("note")))) {
    			$user_id = isset(Auth::user()->id) ? Auth::user()->id : null;
                $note_raw = Note::where(function ($query) use($user_id) {
                    $query->where('user_id', $user_id)
                          ->orWhere('public', 1);
                })
                ->where("id",Input::get("note"))    			
			    ->get()
			    ->first();
                // If either the note is public or this user owns it
    			if(!is_null($note_raw)){
        			// editable true if the user is the user
        			if(isset(Auth::user()->id) && $note_raw->user_id == Auth::user()->id) {
            			$Parsedown = new Parsedown();
                        $note = Input::get("edit",0) ? preg_replace('#<br\s*/?>#i', "", $note_raw->note) : $Parsedown->text($note_raw->note);
                        $editing = Input::get("edit",0) ? 1 : 0;
                        return View::make("note")
                            ->with("note",$note)
                            ->with("id",$note_raw->id)
                            ->with("public",$note_raw->public)
                            ->with("editable",true)
                            ->with("editing",$editing);
                    // note doesn't belong to the user, can't be edited              			
        			} else {
                        $Parsedown = new Parsedown();
                        $note = $Parsedown->text($note_raw->note);                        
                        return View::make("note")
                            ->with("note",$note)
                            ->with("id",$note_raw->id)
                            ->with("public",$note_raw->public)
                            ->with("editing",0)
                            ->with("editable",0); 
        			}  			
    			} else {
        			// Note exists but isn't viewable by the current user (if the user even exists)
        			return View::make("note")
        			    ->with("note","This note is private")
        			    ->with("id","0")
        			    ->with("editing",0)
        			    ->with("public",0)
        			    ->with("editable",0);
    			}
            // If it's the example note, send back the example note text
			} else if(Input::get("note")) {
       			$Parsedown = new Parsedown();
                $note = Input::get("edit",0) ? preg_replace('#<br\s*/?>#i', "", NotesController::$example_text) : $Parsedown->text(NotesController::$example_text);
                $editing = Input::get("edit",0) ? 1 : 0;
                return View::make("note")
                    ->with("note",$note)
                    ->with("id","example")
                    ->with("public",1)
                    ->with("editable",0)
                    ->with("editing",$editing);                
            // If the note doesn't exist
            } else { 
    			return View::make("note")   
    			    ->with("note","That note doesn't exist")
			        ->with("id","0")
			        ->with("editing",0)
			        ->with("public",0)
                    ->with("editable",0);
			} 		
        } else {
        // this is a blank note 
            // if this is a logged in user OR what appears to not be a first time visitor, just return the blank pack
            if(isset(Auth::user()->id) || !is_null(Cookie::get('blankslatefirstime'))) {
                Log::info(Cookie::get('blankslatefirstime'));
    			return View::make("note")
    			    ->with("editing",1)
    			    ->with("editable",1);            
            } else {
            // if this appears to be a first time user, pass something to let the front end know to give a tutorial
                Log::info(Cookie::get('blankslatefirstime'));
                $forever = Cookie::queue('blankslatefirstime', true, 60*24*365);
                return View::make("note")
    			    ->with("editing",1)
    			    ->with("editable",1)
    			    ->with("firsttime",1);            
            }	
		}
	}

	public function save() {
		// routing function for save or create
		if(Input::get("id")) {
			return $this->update();
		} else {
			return $this->create();
		}
	}

	public function create()
	{
        Log::info(Input::get("note_text"));
        Log::info(nl2br(Input::get("note_text")));
		$note = new Note;
		$note->note = Input::get("note_text");
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

	public function update() {
        Log::info(Input::get("note_text"));
        Log::info(nl2br(Input::get("note_text")));
		$note = Note::find(Input::get("id"));
		$note->note = Input::get("note_text");
		$note->save();
		return Response::json(array('success' => true, 'insert_id' => null, 'saved' => true), 200);
	}

	public function data() {
		$notes = Note::where('user_id', Auth::user()->id)->where('archived',"!=",1)->get();
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

	public function archives() {
		$notes = Note::where('user_id', Auth::user()->id)
		    ->where('archived',1)->get();
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

    public function publish() {
		Log::info(Input::get("id"));
		$id = Input::get("id");
		$note = Note::find($id);
		$note->public = !Input::get("publish");
		$note->save();
		return Response::json(array('success' => true, "published" => !Input::get("publish")), 200);        
    }
    
	public function edit() {
		Log::info(Input::get("id"));
		$id = Input::get("id");
		$note = Note::find($id);
		$note->note = Input::get("note_text");
		Log::info(nl2br(Input::get("note_text")));
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
	public function archive() {
		Log::info(Input::get("id"));
		$id = Input::get("id");
		$note = Note::find($id);
		$note->archived = 1;
		$note->save();
		return "success";
	}
	// restore archived note
	public function restore() {
		Log::info(Input::get("id"));
		$id = Input::get("id");
		$note = Note::find($id);
		$note->archived = 0;
		$note->save();
		return "success";
	}
	
    public static $example_text = 
"#This is a blank slate

Each time you open this tab, you'll have a blank page ready for you to fill. You can do lots of things with blank slates, with more coming! This is a helpful guide for your first time, to get started delete it or to see what it looks like go [here](blankslate.io/?note=example)
- You can make a list!
    - Things can be **bold** or *italicized*
    - ~~you can cross things off your list like this~~
- To save your note, hit command + save (or the save button) 

## You can add sub-titles with two hash-tags (one is a title - see the top ^)";
}
