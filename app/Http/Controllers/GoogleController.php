<?php namespace App\Http\Controllers;

use Session;
use Log;
use Input;
use Google_Client;use Google_Service_Drive;use Gdoc;use Google_Service_Drive_DriveFile;
use Response;
use Parsedown;

// Dotenv::load(__DIR__ .'/../../');

class GoogleController extends Controller {
    // test Google API
    // Response::json(array('success' => true, 'insert_id' => null), 200)
	public function addDoc() {
        // start the Google Client variable and set it up
        $client_id = getenv('GOOGLE_CLIENT_ID');
        $client_secret = getenv('GOOGLE_CLIENT_SECRET');
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        $redirect_uri = $root;

        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->setAccessType('offline');
        $client->addScope("https://www.googleapis.com/auth/drive.file");
        if (isset($_REQUEST['logout'])) {
            Session::forget('upload_token');
        }

        if (!empty(Session::get('upload_token')) && Session::get('upload_token')) {
            $client->setAccessToken(Session::get('upload_token'));
            if ($client->isAccessTokenExpired()) {
                Log::info("RERESHING TOKEN!");
                $client->refreshToken(Auth::user()->google_refresh_token);
                Session::forget('upload_token');
                Session::put('upload_token',$client->getAccessToken());
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
        if(Gdoc::where("note_id",$id)->count() === 0) {
            $result = $this->addGdoc($service,$note);
        } else {
            $fileid = Gdoc::where("note_id",$id)->first()->gdocs_id;
            $result = $this->updateGdoc($service,$note,$fileid);
        }
        // add it to the database
        $gdoc = new Gdoc;
        $gdoc->note = $note;
        $gdoc->link = $result["alternateLink"];
        $gdoc->gdocs_id = $result["id"];
        $gdoc->note_id = $id;
        $gdoc->save();
        return Response::json(array('gdoc_link' => $result["alternateLink"]), 200);
	}

	private function addGdoc($service,$note) {
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
        $file->setTitle($this->findTitle($parsed_note));
        $result = $service->files->insert(
            $file,
            array(
            'data' => file_get_contents(TESTFILE),
            'convert' => true,
            'uploadType' => 'multipart'
            )
        );
        unlink('testupload.html');
        return $result;
	}

	private function updateGDoc($service,$note,$fileid) {
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
        $result = $service->files->update(
            $fileid,
            $file,
            array(
            'data' => file_get_contents(TESTFILE),
            'convert' => true,
            'uploadType' => 'multipart'
            )
        );
        unlink('testupload.html');
        return $result;
	}

	private function addGSheet($service,$note) {
        DEFINE("TESTFILE", 'testupload.csv');
        if (!file_exists(TESTFILE)) {
            $myfile = fopen(TESTFILE, "w") or die("Unable to open file!");
            $body = $parsed_note;
            fwrite($myfile, $body);
            fclose($myfile);
        }

        // Now lets try and send the metadata as well using multipart!
        $file = new Google_Service_Drive_DriveFile();
        $file->setTitle($this->findTitle($note));
        $result = $service->files->insert(
            $file,
            array(
            'data' => file_get_contents(TESTFILE),
            'convert' => true,
            'uploadType' => 'multipart'
            )
        );
        unlink('testupload.csv');
        return $result;
	}
}
