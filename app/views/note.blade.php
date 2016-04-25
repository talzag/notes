<!DOCTYPE html contenteditable>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" contenteditable> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" contenteditable> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" contenteditable> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" > <!--<![endif]-->
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
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,700,300,400" rel="stylesheet" type="text/css">
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
</head>

<body id="@if(isset($note)){{$id}}@endif" published="@if(isset($public)){{$public}}@endif" class="{{ 'auth'.Auth::check() . ' editable'.$editable . ' editing'.$editing }}@if(isset($firsttime)) {{'firsttime'}}@endif">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

<!-- FIRST TIME INTRO - ONLY IF THIS APPEARS TO ABSOLUTELY BE YOUR FIRST TIME WILL THIS POP OUT. I'M NOT SURE THIS IS THE BEST WAY TO DO THIS -->
    <div id="firsttime-info-screen" class="menu-slide-out" contenteditable="false">
        <span class="glyphicon glyphicon-menu-close info-close" aria-hidden="true"></span>
        <ul>
            <h3>You're writing your first blank slate!</h3>
            <p>Here are the most important things when writing blank slates.</p>
            <li>First: Bookmark this page (<span class='command-key'></span> + d). Each time you need to write something down, click the bookmark and just start typing!</li>
            <li>Style your slates with <a target="_blank" href="http://daringfireball.net/projects/markdown/syntax">markdown</a>. <a href="?note=example&edit=true" target="_blank">Here's an example</a> and <a href="?note=example" target="_blank">the result when viewed</a></li>
            <li>To save, press "<span class='command-key'></span> + s" at any time or click "save" in the bottom right</li>
            <li>To save as a Google Doc, press "<span class='command-key'></span> + g"</li>
            <li>To download as a PDF, press "<span class='command-key'></span> + p"</li>
        </ul>
    </div>

<!-- TOP STATUS BAR WITH BUTTONS -->
    <div class="note-container">
        <div class="status-bar" contenteditable="false">
            <a class="close"></a><a href="/" class="top-left">+ blank slate</a>
            <a class="round-button full-round-button login-button">login</a>
            <a class="round-button full-round-button view-external-link @if(isset($google_doc)){{ 'google-doc show'}}@endif" href="@if(isset($google_doc)){{$google_doc}}@endif" target="_blank">external link</a>
            <a class="round-button full-round-button view-note">view note</a>
            <a href="notes" class="all-notes round-button full-round-button" contenteditable="false">all notes</a>
        </div>


<!-- VARIOUS OVERLAYS/POPINS FOR LOGGING IN, SIGNING UP, LEARNING MORE, ETC. -->
        <div id="info-screen" class="popin" contenteditable="false">
            <div class="overlay"></div>
            <ul class="popin-list">
                <h3>Style your notes with Markdown</h3>
                <li>Titles: #This will be a title (##this is a sub-title)</li>
                <li>Bold (<span class='command-key'></span> + b): **this will be bold**</li>
                <li>Italics (<span class='command-key'></span> + i): *italics*</li>
                <li>Link: [click here](https://source-url.com)</li>
                <li>- this starts a list</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;* sub-list item 1</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;* sub-list item 2</li>
                <li><a href="http://daringfireball.net/projects/markdown/syntax">More</a></li>
                <h3>Keyboard shortcuts</h3>
                <li><span class='command-key'></span> + s: Save your note</li>
                <li><span class='command-key'></span> + g: Save as Google Doc</li>
                <li><span class='command-key'></span> + p: Download as a PDF</li>
                <li>To publish as a blog, just use a #title at the top and then "publish" on the view-note screen</li>
                <li><a class="round-button semi-round-button more-info" target="_blank" href="http://daringfireball.net/projects/markdown/syntax">Markdown Info</a><a class="round-button semi-round-button close-info">Close</a></li>
            </ul>
        </div>

        <!-- create temporary of permanent user -->
        <div id="choose-user-type" class="popin" contenteditable="false">
            <div class="overlay"></div>
            <ul class="popin-list">
                <li> <h3>Create a user or save note as a guest? Hint: guests can not access their notes on other devices and will lose notes if they clear their cookies.</h3></li>
                <li>
                  <!-- <a class="round-button semi-round-button google-user">signup with google</a> -->
                  <a class="round-button semi-round-button permanent-user">signup for permanent user</a>
                  <a class="guest-user">create guest user</a>
                  <!-- <a class="guest-user">continue as guest</a> -->
                </li>
            </ul>
        </div>

        <div id="loading-screen" class="popin" contenteditable="false">
            <div class="overlay"></div>
            <span class="loading-text">Saving.....</span>
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
                            <a class="forgot-password-link">Forgot Password?</a>
            			</ul>
            		{{ Form::close() }}
                    {{ Form::open(["route" => "sessions.store","class" => "user-management-form signup-form"]) }}
                    <form class="user-management-form signup-form">
            			<ul>
                			<li>{{ Form::email("email","",array('placeholder'=>'What is your email?')) }}</li>
                			<li>{{ Form::password("password",array("placeholder"=>"Choose a password")) }}</li>
                			{{ Form::hidden("url","") }}
                            {{ Form::submit("Create account",array("class"=>"round-button semi-round-button")) }}
                            <button class="round-button semi-round-button close-info">Close</button>
            			</ul>
                    </form>
                    {{ Form::close() }}
                    <form class="user-management-form forgot-password-form">
                        <ul>
                			<li><input type="email" name="email" placeholder="What is your email?"></li>
                            <input type="submit" class="round-button semi-round-button" value="Send Reset Email">
                            <button class="round-button semi-round-button close-info">Close</button>
            			</ul>
                    </form>
                </li>
            </ul>
        </div>
<!-- end of overlays -->

<!-- THIS IS THE SHOW - THIS IS WHERE WE'RE EITHER EDITING OR VIEWING A NOTE -->
    @if(isset($editing) && $editing == 1)
        <textarea id="note-area" class="note-area" placeholder="Just start typing">@if(isset($note)){{$note}}@endif</textarea>
        <div class="note-result"></div>
    @else
        <div class="note-area" contenteditable="false">
            <span class="view-note-toolbar">
                <a class="single-note-edit" href="@if(isset($note)){{'?note='.$id.'&edit=1'}}@endif">edit</a>
                <a class="single-note-publish" href="/">@if(isset($note) && $public == false){{'publish'}}@else{{ 'make private' }}@endif</a>
            </span>
            {{$note}}
        </div>
    @endif
<!-- THIS IS THE END OF THE SHOW - WHERE WE'RE DONE VIEWING OR EDITING OR MAKING A NOTE AND ARE ON TO MORE JANITORIAL WORK -->


    </div><!--note-container-->
    <span class="bottom-left-buttons-container bottom-buttons-container">
        <span class="show-output-button round-button full-round-button bottom-left-button" contenteditable="false"></span>
    </span>
    <span class="bottom-buttons-container" contenteditable="false">
        <span class="compose-info-button round-button full-round-button" contenteditable="false">instructions</span>
        <span class="save-buttons-container" contenteditable="false">
            <span class="save-pdf-button alt-save-button round-button full-round-button" contenteditable="false">pdf <span class="shortcut-instructions">(<span class='command-key'></span> + p)&nbsp&nbsp</span></span>
            <span class="save-google-doc-button round-button alt-save-button full-round-button" contenteditable="false">gdoc <span class="shortcut-instructions">(<span class='command-key'></span> + g)</span></span>
            <span class="save-button round-button full-round-button" contenteditable="false">save <span class="shortcut-instructions">(<span class='command-key'></span> + s)</span></span>
        </span>
    </span>

    <script src="js/vendor/jquery-1.11.1.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/shortcuts.js"></script>
    <script src="js/newnote.js"></script>
    <script src="js/mobile.js"></script>
    <script src="js/typing.js"></script>
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
