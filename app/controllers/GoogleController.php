<?php

class GoogleController extends BaseController {
    // test Google API
//     Response::json(array('success' => true, 'insert_id' => null), 200)
	public function test() {
    
        $client_id = '841513879584-fl6eokkpeut12lj2g328dmj64eqbc6s7.apps.googleusercontent.com';
        $client_secret = 'PqQPkpcCMycSyDjg7QT1I-GY';
        $redirect_uri = 'http://localhost/notes/public/google';
        
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("https://www.googleapis.com/auth/drive");
        $service = new Google_Service_Drive($client);

        if (isset($_REQUEST['logout'])) {
          Session::forget('upload_token');
        }
        
/*
        if (isset($_GET['code'])) {
          $client->authenticate($_GET['code']);
          Session::put('upload_token', $client->getAccessToken());
          $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
          return Redirect::to($redirect_uri);
        }
*/
        
        if (!empty(Session::get('upload_token')) && Session::get('upload_token')) {
          $client->setAccessToken(Session::get('upload_token'));
          if ($client->isAccessTokenExpired()) {
            Session::forget('upload_token');
          }
        } else {
          $authUrl = $client->createAuthUrl();
          return Redirect::to($authUrl);
        }
        
        // If we have an accessToken and everything is good to go, build the file and upload it
        if ($client->getAccessToken()) {
            // build a file that we're going to upload, wait shouldn't I do that later? 
            DEFINE("TESTFILE", 'testupload.html');
            if (!file_exists(TESTFILE)) {
                $myfile = fopen(TESTFILE, "w") or die("Unable to open file!");
                $head = "<html><head></head><body>";
                $body = Input::get("note_text");
                $footer = "</body></html>";
                fwrite($myfile, $head);
                fwrite($myfile, $body);
                fwrite($myfile, $footer);
                fclose($myfile);
            }
                
            // Now lets try and send the metadata as well using multipart!
            $file = new Google_Service_Drive_DriveFile();
            $file->setTitle("Hello World!");
            $result = $service->files->insert(
              $file,
              array(
                'data' => file_get_contents(TESTFILE),
                'convert' => true,
                'uploadType' => 'multipart'
              )
            );
            unlink('testupload.html');
        }
	}
    public function viewtest() {
        return View::make('google');
    }
}