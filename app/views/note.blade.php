<!DOCTYPE html contenteditable>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" contenteditable> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" contenteditable> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" contenteditable> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" contenteditable> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
</head>
<body id="@if(isset($note)){{$id}}@endif" class="{{ 'auth'.Auth::check() . ' editable'.$editable . ' editing'.$editing }}">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div class="status-bar" contenteditable="false"><a class="close"></a><p>notes</p><a class="round-button full-round-button login-button">login</a><a class="round-button full-round-button view-note">view note</a><a href="notes" class="all-notes round-button full-round-button" contenteditable="false">all notes</a></div>
    <div id="info-screen" class="popin" contenteditable="false">
        <div class="overlay"></div>
        <ul>
            <h3>Markdown (how to style your notes)</h3>
            <li>Bold: **this will be bold**</li>
            <li>Italics: *italics*</li>
            <li>Link: [click here](https://source-url.com)</li>
            <li>List: - this starts a list</li>
            <li><a href="http://daringfireball.net/projects/markdown/syntax">More</a></li>
            <h3>Keyboard shortcuts</h3>
            <li>Command + S: Save your note</li>
            <li>Command + B: Make your note a blog (coming soon)</li>
            <li><a class="round-button semi-round-button more-info" target="_blank" href="http://daringfireball.net/projects/markdown/syntax">More Info</a><a class="round-button semi-round-button close-info">Close</a></li>
        </ul>
    </div>
<!--     create temporary of permanent user -->
    <div id="choose-user-type" class="popin" contenteditable="false">
        <div class="overlay"></div>
        <ul>
            <li> <h3>Create a user or save note as a guest? Hint: guests can not access their notes on other devices and will lose notes if they clear their cookies.</h3></li>
            <li><a class="round-button semi-round-button guest-user">create guest user</a><a class="round-button semi-round-button permanent-user">signup for permanent user</a></li>
        </ul>
    </div>
    <div id="login-screen" class="popin" contenteditable="false">
        <div class="overlay"></div>
        <ul>
            <li>
        		{{ Form::open(["route" => "sessions.store","class" => "user-management-form login-form"]) }}
        			<ul>
            			<li>{{ Form::email("email","",array('placeholder'=>'Email')) }}</li>
            			<li>{{ Form::password("password",array('placeholder'=>'Password')) }}</li>
            			{{ Form::hidden("url","") }}
            			{{ Form::submit("Login",array("class"=>"round-button full-round-button")) }}
        			</ul>
        		{{ Form::close() }}
        		{{ Form::open(["route" => "sessions.store","class" => "user-management-form signup-form"]) }}
        			<ul>
            			<li>{{ Form::email("email","",array('placeholder'=>'What is your email?')) }}</li>
            			<li>{{ Form::password("password",array("placeholder"=>"Choose a password")) }}</li>
            			{{ Form::hidden("url","") }}
            			<li>{{ Form::submit("Create account",array("class"=>"round-button full-round-button")) }}</li>
        			</ul>
        		{{ Form::close() }}        		
            </li>
            <li><a class="round-button semi-round-button close-info">Close</a></li>
        </ul>		
    </div>
@if(isset($editing) && $editing == 1)
    <textarea class="note-area" placeholder="Just starting typing">@if(isset($note)){{$note}}@endif</textarea>    
@else
     <div class="note-area" contenteditable="false"><a class="single-note-edit" href="@if(isset($note)){{'?note='.$id.'&edit=1'}}@endif">edit</a>{{$note}}</div>
@endif
    <span class="save-button round-button full-round-button" contenteditable="false">Save</span>
    <span class="compose-info-button round-button full-round-button" contenteditable="false">instructions</span>
    <script src="js/vendor/jquery-1.11.1.min.js"></script>
    <!-- <script src="js/plugins.js"></script> -->
    <script src="js/newnote.js"></script>
    <script>
		function replaceHtml(string_to_replace) {
		    return $("<div>").append(string_to_replace.replace(/&nbsp;/g, ' ').replace(/<br.*?>/g, '&#13;&#10;')).text();
		}
		console.log();	    
	</script>
</body>
</html>
