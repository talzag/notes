<?php

class NotesController extends BaseController {
    // the root "note" url logic. Either it's a new note or an existing note.
	public function note() {
    	
    	if(Input::get("code")) {
        	return $this->handle_google_login();
    	}
        // if this is just a regular, ole' note without google stuff
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
                        // if there's a google auth code, we do different shit
                        if (Input::get("gdoc")) {
                            Log::info("gdoc");
                            return $this->handle_google_note_add($note_raw->note,Input::get("note"));
                        // else if gdoc was added, return some extra information to the view
                        } else if(Input::get("gdoc_added")) {
                            return $this->gdocs_success_view($note,$note_raw,Input::get("note"));
                        // else return the view
                        } else {
                             return View::make("note")
                                ->with("note",$note)
                                ->with("id",$note_raw->id)
                                ->with("public",$note_raw->public)
                                ->with("editable",true)
                                ->with("editing",$editing);                           
                        }
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
    			return View::make("note")
    			    ->with("editing",1)
    			    ->with("editable",1);

            } else {
            // if this appears to be a first time user, pass something to let the front end know to give a tutorial
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
		$note = new Note;
		$note->note = htmlspecialchars(Input::get("note_text"));
		// this needs to chekc if user exists, if not great a temp one and store the session in the browser somehow
		if(Auth::check()) {
			$note->user_id = Auth::user()->id;
			$note->save();
			return Response::json(array('success' => true, 'insert_id' => $note->id), 200);
		} else {
			return Response::json(array('success' => false, 'insert_id' => null), 201);
		}
	}

	public function update() {
		$note = Note::find(Input::get("id"));
        if (Auth::check() and Auth::id() == $note->user_id) {
            $note->note = htmlspecialchars(Input::get("note_text"));
            $note->save();
            return Response::json(array('success' => true, 'insert_id' => null, 'saved' => true), 200);
        } else {
            return Response::json(array('success' => false, 'insert_id' =>null, 'saved' => false), 201);
        }
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
        $note = Note::find(Input::get('id'));
        if (Auth::check() and Auth::id() == $note->user_id) {
            $note->public = !Input::get("publish");
            $note->save();
            return Response::json(array('success' => true, "published" => !Input::get("publish")), 200);
        } else {
            return Response::json(array('success' => false, "published" => Input::get("publish")), 201);
        }
    }

	public function edit() {
		$note = Note::find(Input::get("id"));
        if (Auth::check() and Auth::id() == $note->user_id) {
            $note->note = Input::get("note_text");
            $note->save();
            return "success";
        }
    }

	public function delete() {
		$note = Note::find(Input::get("id"));
        if (Auth::check() and Auth::id() == $note->user_id) {
            $note->delete();
            return "success";
        }
	}
	public function archive() {
		$note = Note::find(Input::get("id"));
        if (Auth::check() and Auth::id() == $note->user_id) {
            $note->archived = 1;
            $note->save();
            return "success";
        }
	}
	// restore archived note
	public function restore() {
		$note = Note::find(Input::get("id"));
        if (Auth::check() and Auth::id() == $note->user_id) {
            $note->archived = 0;
            $note->save();
            return "success";
        }
	}
	// If the browser has Google authorization information, use it to auth adding of Google Docs
	private function handle_google_login() {
        // If you get a google response code, do some shit and redirect
        Log::info("Handle google login working");
        $client_id = getenv('GOOGLE_CLIENT_ID');
        $client_secret = getenv('GOOGLE_CLIENT_SECRET');
        $redirect_uri = 'http://localhost';
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("https://www.googleapis.com/auth/drive");
        $service = new Google_Service_Drive($client);
        $tokens = $client->authenticate(Input::get("code"));
        Log::info($tokens);
        if(isset(json_decode($tokens)->refresh_token)) {
            $user = Auth::user();
            $user->google_refresh_token = json_decode($tokens)->refresh_token;
            $user->save();            
        }
        Session::put('upload_token', $client->getAccessToken());
        return Redirect::to($redirect_uri."?note=".Input::get("state")."&edit=true&gdoc=true");    	
    }
    // Handles first time google docs auth note adding
    private function handle_google_note_add($note,$note_id) {
		$params = array(
			"note_text"=>$note,
			"id"=>$note_id
		);
		$request = Request::create('google/addDoc', 'POST',$params);
		Request::replace($request->input());
		$addGoogleDoc = Route::dispatch($request)->getContent();
		return Redirect::to("http://localhost?note=".$note_id."&edit=true&gdoc_added=true");       
    }
    // handle when gdocs was successfully added
    private function gdocs_success_view($note,$note_raw,$note_id) {
        Log::info("show gdocs success view");
        $gdoc = Gdoc::where("note_id",$note_id)->first();
        // return the view with all the data!
        return View::make("note")
            ->with("note",$note)
            ->with("id",$note_raw->id)
            ->with("public",$note_raw->public)
            ->with("editable",true)
            ->with("editing",true)
            ->with("google_doc",$gdoc->link);        
    }

    private static $example_text =
        "#This is a blank slate

        You can do lots of things with [blank slates](http://blankslate.io), with more coming!

        - You can make a list!
            - Lists can have sub list, you can link to [things](http://blankslate.io/?note=53) and even make public notes (like that one).
            - Things can be **bold** or *italicized*
        - ~~you can cross things off your list like this~~
        - To save your note, hit command + save (or the save button)

        ## You can add sub-titles with two hash-tags (one is a title - see the top ^)";
        
    
}
