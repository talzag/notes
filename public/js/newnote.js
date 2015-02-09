$("textarea.note_area").focus();
function log(text) {
	console.log(text);
}
$(document).keydown(function(e){ 
// 	log(e.keyCode);
	// SAVE: Behavior for Control/Command S - SAVE
    if (e.metaKey == true && e.keyCode === 83) {
	    e.preventDefault();
	    saveNote();
    }
    // BLOG: Behavior for making a note into a title + body Command + B: BLOG
    else if(e.metaKey == true && e.keyCode === 66) {
	    e.preventDefault();
	    
    }
});
// compose notes button click functionality
$(".save-button").click(function(e) {
	    e.preventDefault();
	    saveNote();	
});

function saveNote() {
	        log("SAVE");
        $.ajax({
	        url: "notes/create",
	        type:"POST",
	        data: {
		        note:$("textarea.note_area").val(),
	        },
	        success:function(data) {
		        log(data);
		        if(data === "success") {
			        showSuccess("note saved!","slow");
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
		        log("error");
	        }
        })
}

function createTempUser() {
	log("CREATE TEMP USER");
    $.ajax({
        url: "users/guest",
        type:"POST",
        data: {
	        note: $("textarea.note_area").val()
        },
        success:function(data) {
	        log(data);
	        if(data = "success") {
		        showSuccess("successfully created temp-user and your first note", 3000);
		        $("textarea.note_area").val("");
	        }
        },
        error:function() {
	        log("error");
        }
    })
}

function createNewUser() {
	// This should be a model where they make an account
	var email = prompt("New User Email");
	var password = prompt("New User Password");
    $.ajax({
        url: "users/create",
        type: "POST",
        data: {
	        email: email,
	        password: password,
	        note: $("textarea.note_area").val()
        },
        success:function(data) {
	        log(data);
	        if(data = "success") {
		        showSuccess("successfully created user and your first note", 3000);
		        $("textarea.note_area").val("");
	        }
        },
        error:function() {
	        log("error");
        }
    })
}

// click events for UI on add notes screen
$(".status-bar a.close").click(function() {
	hideSuccess("notes","slow");
});
// show info screen
$(".compose-info-button").click(function() {
	$(".info-screen").fadeIn("fast");
});
$(".close-info").click(function() {
	$(".info-screen").fadeOut("fast");
});
// hide info screen
$(".info-screen .overlay").click(function() {
	$(".info-screen").fadeOut("fast");
});
// show success
function showSuccess(text, speed) {
	$(".status-bar").addClass("success");
	$(".success p").text(text);
// 	$(".success").fadeIn("slow");
// 	hideSuccess(speed);
}

function hideSuccess(text,speed) {
	$(".success p").text(text);
	$(".status-bar").removeClass("success");
	$("textarea.note_area").focus();
}