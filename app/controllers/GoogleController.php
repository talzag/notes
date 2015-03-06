<?php

Dotenv::load(__DIR__ .'/../../');

class GoogleController extends BaseController {
    // test Google API
//     Response::json(array('success' => true, 'insert_id' => null), 200)
	public function test() {
        /************************************************
          We'll setup an empty 1MB file to upload.
         ************************************************/
        DEFINE("TESTFILE", 'testupload.html');
        if (!file_exists(TESTFILE)) {
            $myfile = fopen(TESTFILE, "w") or die("Unable to open file!");
            $head = "<html><head></head><body>";
            $body = "<h1>Hello World</h1><p>HI!!!!</p>";
            $footer = "</body></html>";
            fwrite($myfile, $head);
            fwrite($myfile, $body);
            fwrite($myfile, $footer);
            fclose($myfile);
        }

        /************************************************
          ATTENTION: Fill in these values! Make sure
          the redirect URI is to this page, e.g:
          http://localhost:8080/fileupload.php
         ************************************************/
        $client_id = getenv('GOOGLE_CLIENT_ID');
        $client_secret = getenv('GOOGLE_CLIENT_SECRET');
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
        
        if (!empty(Session::get('upload_token')) && Session::get('upload_token')) {
          $client->setAccessToken(Session::get('upload_token'));
          if ($client->isAccessTokenExpired()) {
            Session::forget('upload_token');
          }
        } else {
          $authUrl = $client->createAuthUrl();
          return Redirect::to($authUrl);
        }

        /************************************************
          If we're signed in then lets try to upload our
          file. For larger files, see fileupload.php.
         ************************************************/
        if ($client->getAccessToken()) {

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
