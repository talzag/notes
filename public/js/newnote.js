$("textarea.note_area").focus();

function log(text) {
	console.log(text);
}

$(document).keydown(function(e){
    // log(e.keyCode);
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
        dataType:"JSON",
        data: {
            note_text:$("textarea.note_area").val(),
            id:$("body").attr("id")
        },  
        statusCode: {
			200: function(data) {
				console.log(data.insert_id);
				if(data.insert_id !== null) {
	                showSuccess("new note created!","slow");
	                $("textarea.note_area").val("");
	                $(".view-note").attr("href","?note=" + data.insert_id);					
				} else {
					showSuccess("note saved!","slow");
					$(".view-note").hide();
				}
			},
			201: function() {
                var c = confirm("Create a user or save note as a guest? Hint: guests can not access their notes on other devices and will lose notes if they clear their cookies.");
                if(c) {
                    createNewUser();
                } else {
                    createTempUser();
                }				
			},
			500: function() {
				alert("Something went wrong saving your note - email tommy@painless1099.com and yell at him about it");
			}
  		},
  		success:function(data) {
	  		console.log(data.status);
  		},
        error:function() {
            log("error");
        }
    });
}

function createTempUser() {
	log("CREATE TEMP USER");
    $.ajax({
        url: "users/guest",
        type:"POST",
        data: {
	        note_text: $("textarea.note_area").val()
        },
        success:function(data) {
	        log(data);
	        if(data === "success") {
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
	$("#login-screen").fadeIn("fast");
	$(".signup-form").show();	
}

// if signup form is submitted, block it and submit via AJAX
$("form.signup-form").submit(function(e) {
    e.preventDefault();
    var form = $(this).serialize()+"&note_text="+$("textarea.note_area").val(); 
    $.ajax({
        url: "users/create",
        type: "POST",
        dataType:"json",
        data: form,
        success:function(data) {
	        log(data);
	        log(data.success);
	        if(data.success) {
    	        // hide screens we don't need and set href of "view note"
    	        $("#login-screen").fadeOut("fast");
    	        $(".login-button").hide();
    	        $(".all-notes").show();
    	        $(".view-note").show().attr("href","?note="+data.insert_id);
		        showSuccess("successfully created user and your first note", 3000);
		        $("textarea.note_area").val("");
	        }
        },
        error:function() {
	        log("error");
        }
    }) 
})

// click events for UI on add notes screen
$(".status-bar a.close").click(function() {
	hideSuccess("notes","slow");
});

// show info screen
$(".compose-info-button").click(function() {
	$("#info-screen").fadeIn("fast");
});
$(".close-info").click(function() {
	$("#info-screen").fadeOut("fast");
});
// hide info screen
$("#info-screen .overlay").click(function() {
	$("#info-screen").fadeOut("fast");
});

// show login screen
$(".login-button").click(function() {
	$("#login-screen").fadeIn("fast");
	$(".login-form").show();
});
$(".close-login").click(function() {
	$("#login-screen").fadeOut("fast");
});
// hide login screen
$("#login-screen .overlay").click(function() {
	$("#login-screen").fadeOut("fast");
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
