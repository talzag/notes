<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Blank Slate</title>
    <meta name="description" content="Get thoughts down quick, do things with them later. Just start typing">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--     <meta name="apple-mobile-web-app-capable" content="yes"> -->
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/main.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
</head>
<body class="{{ "temp".Auth::user()->is_temporary }}">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <a class="create-permanent-user round-button">Create Permanent User</a>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped display right-two-blank" id="archives_table" width="100%">
            <thead>
                <tr>
                    <th>Last Updated</th>
                    <th>Created</th>
                    <th>Archives<input type="search" class="round-button full-round-button" placeholder="search archives" aria-controls="archieves_table"></th>
                </tr>
            </thead>
        </table>
    </div>
        <div class="menu-slide-out">
            <span class="hamburger-icon"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span></span>
            <ul>
                <li class="create-permanent-user"><a class="create-permanent-user round-button">Create Permanent User</a></li>
                <li><a class="full-round-button round-button" href="/">New Note</a></li>
                <li><a class="full-round-button round-button" href="notes">All Notes</a></li>
                @if (Auth::user()->is_temporary == 0)
                <li><a class="full-round-button round-button" href="logout">Logout</a></li>
                @endif
            </ul>
        </div>
    <script src="js/vendor/jquery-1.11.1.min.js"></script>
    <script src="js/vendor/jquery.dataTables.min.js"></script> <!-- Edited for search input -->
    <script src="js/vendor/jquery.dataTables.editable.js"></script>
    <script src="js/vendor/bootstrap-datatables.js"></script>
    <script src="js/datatables/archives.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    
      ga('create', 'UA-60041451-1', 'auto');
      ga('send', 'pageview');
    
    </script>

</body>
</html>