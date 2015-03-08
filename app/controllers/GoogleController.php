<?php

Dotenv::load(__DIR__ .'/../../');

class GoogleController extends BaseController {
    // test Google API
//     Response::json(array('success' => true, 'insert_id' => null), 200)
	public function addDoc() {
        Log::info(Input::get("id"));
        // start the Google Client variable and set it up
        $client_id = getenv('GOOGLE_CLIENT_ID');
        $client_secret = getenv('GOOGLE_CLIENT_SECRET');
        $redirect_uri = 'http://localhost';

        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("https://www.googleapis.com/auth/drive");
        if (isset($_REQUEST['logout'])) {
          Session::forget('upload_token');
        }
        
        if (!empty(Session::get('upload_token')) && Session::get('upload_token')) {
          $client->setAccessToken(Session::get('upload_token'));
          if ($client->isAccessTokenExpired()) {
            Session::forget('upload_token');
          }
        } else {
          $state = Input::get("id");
          $client->setState($state);
          $authUrl = $client->createAuthUrl();
          return Response::json(array('auth_url' => $authUrl), 201);
        }

        if ($client->getAccessToken()) {
            return $this->addGoogleDoc($client,Input::get("note_text"),Input::get("id"));
        }
	}
	
	private function addGoogleDoc($client,$note,$id) {
    	    $service = new Google_Service_Drive($client);
            $Parsedown = new Parsedown();
            $parsed_note = $Parsedown->text($note);
            DEFINE("TESTFILE", 'testupload.html');
            if (!file_exists(TESTFILE)) {
                $myfile = fopen(TESTFILE, "w") or die("Unable to open file!");
                $head = "<html><head></head><body>";
                $body = $parsed_note;
                $footer = "</body></html>";
                fwrite($myfile, $head);
                fwrite($myfile, $body);
                fwrite($myfile, $footer);
                fclose($myfile);
            }

          // Now lets try and send the metadata as well using multipart!
          $file = new Google_Service_Drive_DriveFile();
//           $file->setTitle($this->findTitle($parsed_note));
          $file->setTitle("Blank Slate Test");
          $result = $service->files->insert(
              $file,
              array(
                'data' => file_get_contents(TESTFILE),
                'convert' => true,
                'uploadType' => 'multipart'
              )
            );
            Log::info(json_encode($result));
            unlink('testupload.html');
            // add it to the database
            $gdoc = new Gdoc;
            $gdoc->note = $note;
            $gdoc->link = $result["alternateLink"];
            $gdoc->gdocs_id = $result["id"];
            $gdoc->note_id = $id;
            $gdoc->save();
            return Response::json(array('gdoc_link' => $result["alternateLink"]), 200);    	
	}
	
	private function findTitle($text) {
        $dom = new DOMDocument();
        $dom->loadHTML($text);
        $h1s = $dom->getElementsByTagName('h1');
    	return "Test Title";
	}
	
    public function viewtest() {
        return View::make('google');
    }
}
