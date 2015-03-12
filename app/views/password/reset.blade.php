<!DOCTYPE html contenteditable>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" contenteditable> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" contenteditable> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" contenteditable> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" contenteditable> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Blank Slate</title>
    <meta name="description" content="Get thoughts down quick, do things with them later. Just start typing">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, target-densityDpi=device-dpi" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../css/normalize.css">
    <link rel="stylesheet" href="../../css/main.css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,700,300,400" rel="stylesheet" type="text/css">
<!--     <script src="js/vendor/modernizr-2.6.2.min.js"></script> -->
</head>

<body class="reset-password {{ $token }}">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->


        <div id="login-screen" class="popin" contenteditable="false">
            <div class="overlay"></div>
            <ul class="popin-list">
                <li>
                    {{ Form::open(["action" => "RemindersController@postReset","class" => "user-management-form reset-password-form","method"=>"post"]) }}
            			<ul>
                			<li>{{ Form::email("email","",array('placeholder'=>'Email')) }}</li>
                			<li>{{ Form::password("password",array('placeholder'=>'Password')) }}</li>
                			<li>{{ Form::password("password_confirmation",array('placeholder'=>'Password Confirm')) }}</li>
                            {{ Form::hidden("token",$token) }}
                            {{ Form::submit("Reset Password",array("class"=>"round-button semi-round-button")) }}
                            <button class="round-button semi-round-button close-info">Close</button>
            			</ul>
            		{{ Form::close() }}
                </li>
            </ul>
        </div>

<!--     <script src="js/vendor/jquery-1.11.1.min.js"></script> -->
<!--     <script src="js/plugins.js"></script> -->
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
