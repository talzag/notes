$("textarea.note-area").focus();

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
    // if this is a first time user that has typed more than a few lettesr 
    if($("body").hasClass("firsttime") && $("textarea.note-area").val().length > 2) {
        $("body").addClass("menu-showing");
        $("body").removeClass("firsttime")
    }
    // BLOG: Behavior for making a note into a title + body Command + B: BLOG
    else if(e.metaKey == true && e.keyCode === 66) {
	    e.preventDefault();
    }
    if($(".success").is(":visible")) {
        hideSuccess("+ blank slate","slow");
    }
});

// prevent tab functionality 
$("textarea").keydown(function(e) {
  var $this, end, start;
  if (e.keyCode === 9) {
    start = this.selectionStart;
    end = this.selectionEnd;
    $this = $(this);
    $this.val($this.val().substring(0, start) + "\t" + $this.val().substring(end));
    this.selectionStart = this.selectionEnd = start + 1;
    return false;
  }
});

// compose notes button click functionality
$(".save-button").click(function(e) {
	    e.preventDefault();
	    saveNote();
});
function saveNote() {
    log("SAVE");
    if($("textarea.note-area").val().length > 0) {
        $.ajax({
            url: "notes/create",
            type:"POST",
            dataType:"JSON",
            data: {
                note_text:$("textarea.note-area").val(),
                id:$("body").attr("id")
            },  
            statusCode: {
    			200: function(data) {
    				console.log(data.insert_id);
    				if(data.insert_id !== null) {
        				ga('send', 'Notes', 'Save', 'New');
    	                showSuccess("new note created!","slow");
    	                $("textarea.note-area").val("");
    	                $(".view-note").attr("href","?note=" + data.insert_id);					
    				} else {
        				ga('send', 'Notes', 'Save', 'Old');
    					showSuccess("note saved!","slow");
    					$(".view-note").attr("href","?note=" + $("body").attr("id"));	
    				}
    			},
    			201: function() {
                    $("#choose-user-type").fadeIn("fast");			
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
}
$(".guest-user").click(function() {
    createTempUser();
});
$(".permanent-user").click(function() {
    createNewUser();
});
// toggle public / private 
$(".single-note-publish").click(function(e) {
    e.preventDefault();
    $.ajax({
        url: "notes/publish",
        type: "POST",
        dataType: "json",
        data: {
            id: $("body").attr("id"),
            publish: $("body").attr("published")
        },
        success: function(data) {
            showSuccess("public status changed", 3000);
            if(data.published) {
                $(".single-note-publish").text("make private");
                $("body").attr("published",1);
            } else {
                $(".single-note-publish").text("publish");
                $("body").attr("published",0);
            }
            log(data);
        },
        error: function(data) {
            alert("SOMETHING WENT WRONG - email tomasienrbc@gmail.com and yell at him");
            log(data);
        }
    })
});
function createTempUser() {
    ga('send', 'Users', 'New', 'Temporary');
	log("CREATE TEMP USER");
    $.ajax({
        url: "users/guest",
        type:"POST",
        dataType: "json",
        data: {
	        note_text: $("textarea.note-area").val()
        },
        success:function(data) {
	        log(data);
	        if(data.success) {
    	        $(".popin").hide();
		        showSuccess("successfully created temp-user and your first note", 3000);
		        $("textarea.note-area").val("");
    	        $(".all-notes").show();
    	        $(".view-note").show().attr("href","?note="+data.insert_id);
	        }
        },
        error:function() {
	        log("error");
        }
    })
}

function createNewUser() {
	// This should be a model where they make an account
	$(".popin").hide();
	$("#login-screen").fadeIn("fast");
	$(".signup-form").show();	
}

// if signup form is submitted, block it and submit via AJAX
$("form.signup-form").submit(function(e) {
    ga('send', 'Users', 'New', 'Permanent');
    e.preventDefault();
    var form = $(this).serialize()+"&note_text="+$("textarea.note-area").val(); 
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
		        $("textarea.note-area").val("");
	        }
        },
        error:function() {
	        log("error");
        }
    }) 
});

// click events for UI on add notes screen
$(".status-bar a.close").click(function() {
	hideSuccess("notes","slow");
});
// close the first time info screen
$(".info-close").click(function() {
    $("body").removeClass("menu-showing");
    $("textarea.note-area").focus();
})
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
	$(".login-form input[type=email]").focus();
});
// JS Event for login form 
$(".login-form").submit(function() {
    ga('send', 'Users', 'Returning', 'Login');
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
    $("a.top-left").removeAttr('href');
	if($(".status-bar").hasClass("success")) {
    	$(".status-bar").fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);
	} else {
	    $(".status-bar").addClass("success");
        $(".success a.top-left").text(text);    	
	}
}

function hideSuccess(text,speed) {
    $("a.top-left").attr('href',"/");
	$(".success a.top-left").text(text);
	$(".status-bar").removeClass("success");
	$("textarea.note-area").focus();
}
