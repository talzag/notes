<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Laravel PHP Framework | Google Drive test</title>
  <style>
    @import url(//fonts.googleapis.com/css?family=Lato:700);
    body {
      margin:0;
      font-family:'Lato', sans-serif;
      text-align:center;
      color: #999;
    }
    a, a:visited {
      text-decoration:none;
    }
  </style>
</head>
<body>
    <h1>Google Drive Test</h1>

    <?php
      $client = new Google_Client();
      $client->setApplicationName("Laravel");
      $client->setClientId('841513879584-fl6eokkpeut12lj2g328dmj64eqbc6s7.apps.googleusercontent.com');
      $client->setClientSecret('PqQPkpcCMycSyDjg7QT1I-GY');
      $client->setRedirectUri('http://localhost/notes/public/google/viewtest');
      $client->setScopes(array('https://www.googleapis.com/auth/drive'));
      // Step 2: The user accepted your access now you need to exchange it.
      if (Input::has('code')) {
        $accessToken = $client->authenticate(Input::get('code'));
        $client->setAccessToken($accessToken);
      }
      // Step 1:  The user has not authenticated we give them a link to login
      if (!$client->getAccessToken() && !Session::get('token')) {
        $authUrl = $client->createAuthUrl();
        echo "<p><a class='login' href='$authUrl'>Login in</a></p>";
      }
      // Interact with Files
      if ($client->getAccessToken()) {
        $service = new Google_Service_Drive($client);
        //Insert a file
        $file = new Google_Service_Drive_DriveFile();
        $file->setTitle('My document');
        $file->setDescription('A test document');
        $file->setMimeType('text/plain');
        $data = file_get_contents('document.txt');
        $createdFile = $service->files->insert($file, array(
          'data' => $data,
          'uploadType' => 'media',
          'mimeType' => 'text/plain',
        ));
        // Dump created file
        //var_dump($createdFile);
        // Dump file listing
        //echo '......';
        //var_dump($service->files->listFiles());
      }
      // Debug
      echo 'Session: ' . Session::get('token') . '<br />';
      echo 'Code: ' . Input::get('code') . '<br />';
    ?>
  </div>
</body>
</html>