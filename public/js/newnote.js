$("textarea.note_area").focus();
$(document).keydown(function(e){ 
	console.log(e.keyCode);
	// SAVE: Behavior for Control/Command S - SAVE
    if (e.metaKey == true && e.keyCode === 83) {
	    e.preventDefault();
        console.log("SAVE");
        $.ajax({
	        url: "addnote",
	        type:"POST",
	        data: {
		        note:$("textarea.note_area").val(),
	        },
	        success:function(data) {
		        console.log(data);
		        if(data === "success") {
			        showSuccess("success!","slow");
			        $("textarea.note_area").val("");
		        } else if(data === "logged out") {
			        var c = confirm("Create a user or save note as a guest? Hint: guests can not access their notes on other devices and will lose notes if they clear their cookies.");
			    	if(c) {
				    	createNewUser();
			    	} else {
				    	createTempUser();
			    	}    
		        }
	        },
	        error:function() {
		        console.log("error");
	        }
        })
    }
    // BLOG: Behavior for making a note into a title + body Command + B: BLOG
    else if(e.metaKey == true && e.keyCode === 66) {
	    e.preventDefault();
	    
    }
    
});

function createTempUser() {
	console.log("CREATE TEMP USER");
    $.ajax({
        url: "newtempuser",
        type:"POST",
        data: {
	        note: $("textarea.note_area").val()
        },
        success:function(data) {
	        console.log(data);
	        if(data = "success") {
		        showSuccess("successfully created temp-user and your first note", 3000);
		        $("textarea.note_area").val("");
	        }
        },
        error:function() {
	        console.log("error");
        }
    })	
}

function createNewUser() {
	// This should be a model where they make an account
	var email = prompt("New User Email");
	var password = prompt("New User Password");	
    $.ajax({
        url: "newuser",
        type:"POST",
        data: {
	        email:email,
	        password:password,
	        note: $("textarea.note_area").val()
        },
        success:function(data) {
	        console.log(data);
	        if(data = "success") {
		        showSuccess("successfully created user and your first note", 3000);
		        $("textarea.note_area").val("");
	        }
        },
        error:function() {
	        console.log("error");
        }
    })
}

function showSuccess(text, speed) {
	$(".success p").text(text);
	$(".success").fadeIn("slow");
	$(".success").fadeOut(speed);
}