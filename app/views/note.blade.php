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
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,700,300,400' rel='stylesheet' type='text/css'>
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
</head>

<body id="@if(isset($note)){{$id}}@endif" published="@if(isset($public)){{$public}}@endif" class="{{ 'auth'.Auth::check() . ' editable'.$editable . ' editing'.$editing }}@if(isset($firsttime)) {{'firsttime'}}@endif">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div id="firsttime-info-screen" class="menu-slide-out" contenteditable="false">
        <span class="glyphicon glyphicon-menu-close info-close" aria-hidden="true"></span>
        <ul>
            <h3>You're writing your first blank slate!</h3>
            <p>Here are the most important things when writing blank slates.</p>
            <li>First: Bookmark this page (&#8984 + d). Each time you need to write something down, click the bookmark and just start typing!</li>
            <li>Style your slates with <a target="_blank" href="http://daringfireball.net/projects/markdown/syntax">markdown</a>. <a href="?note=example&edit=true" target="_blank">Here's an example</a> and <a href="?note=example" target="_blank">the result when viewed</a></li>
            <li>To save, press "command + s" at any time or click "save" in the bottom right</li>
        </ul>
    </div>

    <div class="note-container">
        <div class="status-bar" contenteditable="false"><a class="close"></a><a href="/" class="top-left">+ blank slate</a><a class="round-button full-round-button login-button">login</a><a class="round-button full-round-button view-note">view note</a><a href="notes" class="all-notes round-button full-round-button" contenteditable="false">all notes</a></div>
        <div id="info-screen" class="popin" contenteditable="false">
            <div class="overlay"></div>
            <ul class="popin-list">
                <h3>Style your notes with Markdown</h3>
                <li>Titles: #This will be a title (## for sub-title)</li>
                <li>Bold: **this will be bold**</li>
                <li>Italics: *italics*</li>
                <li>Link: [click here](https://source-url.com)</li>
                <li>- this starts a list</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;* sub-list item 1</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;* sub-list item 2</li>
                <li><a href="http://daringfireball.net/projects/markdown/syntax">More</a></li>
                <h3>Keyboard shortcuts</h3>
                <li>&#8984 + S: Save your note</li>
                <li>&#8984 + B: Make your note a blog (coming soon)</li>
                <li><a class="round-button semi-round-button more-info" target="_blank" href="http://daringfireball.net/projects/markdown/syntax">Markdown Info</a><a class="round-button semi-round-button close-info">Close</a></li>
            </ul>
        </div>

        <!-- create temporary of permanent user -->
        <div id="choose-user-type" class="popin" contenteditable="false">
            <div class="overlay"></div>
            <ul class="popin-list">
                <li> <h3>Create a user or save note as a guest? Hint: guests can not access their notes on other devices and will lose notes if they clear their cookies.</h3></li>
                <li><a class="round-button semi-round-button guest-user">create guest user</a><a class="round-button semi-round-button permanent-user">signup for permanent user</a></li>
            </ul>
        </div>

        <div id="login-screen" class="popin" contenteditable="false">
            <div class="overlay"></div>
            <ul class="popin-list">
                <li>
            		{{ Form::open(["route" => "sessions.store","class" => "user-management-form login-form"]) }}
            			<ul>
                			<li>{{ Form::email("email","",array('placeholder'=>'Email')) }}</li>
                			<li>{{ Form::password("password",array('placeholder'=>'Password')) }}</li>
                			{{ Form::hidden("url","") }}
                            {{ Form::submit("Login",array("class"=>"round-button semi-round-button")) }}
                            <button class="round-button semi-round-button close-info">Close</button>
            			</ul>
            		{{ Form::close() }}
                    {{ Form::open(["route" => "sessions.store","class" => "user-management-form signup-form"]) }}
            			<ul>
                			<li>{{ Form::email("email","",array('placeholder'=>'What is your email?')) }}</li>
                			<li>{{ Form::password("password",array("placeholder"=>"Choose a password")) }}</li>
                			{{ Form::hidden("url","") }}
                            {{ Form::submit("Create account",array("class"=>"round-button semi-round-button")) }}
                            <button class="round-button semi-round-button close-info">Close</button>
            			</ul>
                    {{ Form::close() }}
                </li>
            </ul>
        </div>

    @if(isset($editing) && $editing == 1)
        <textarea class="note-area" placeholder="Just start typing">@if(isset($note)){{$note}}@endif</textarea>
    @else
         <div class="note-area" contenteditable="false"><span class="view-note-toolbar"><a class="single-note-edit" href="@if(isset($note)){{'?note='.$id.'&edit=1'}}@endif">edit</a><a class="single-note-publish" href="/">@if(isset($note) && $public == false){{'publish'}}@else{{ 'make private' }}@endif</a></span>{{$note}}</div>
    @endif
    </div><!--note-container-->

    <span class="save-button round-button full-round-button" contenteditable="false">Save</span>
    <span class="compose-info-button round-button full-round-button" contenteditable="false">instructions</span>

    <script src="js/vendor/jquery-1.11.1.min.js"></script>
    <!-- <script src="js/plugins.js"></script> -->
    <script src="js/newnote.js"></script>
    <script>
		function replaceHtml(string_to_replace) {
		    return $("<div>").append(string_to_replace.replace(/&nbsp;/g, ' ').replace(/<br.*?>/g, '&#13;&#10;')).text();
		}
	</script>
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
