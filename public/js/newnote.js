$("textarea.note-area").focus();

function log(text) {
	// console.log(text);
}

new Clipboard(".publish-note");

function getBaseURL() {
  pathArray = location.href.split( '/' );
  protocol = pathArray[0];
  host = pathArray[2];
  url = protocol + '//' + host;
  return url;
}

$(".show-output-button").click(function() {
    toggleShowOutput();
})

// compose notes button click functionality
$(".save-button").click(function(e) {
    e.preventDefault();
    var params = ["new note created!","slow"];
    saveNote(showSuccess,params);
});

function saveNote(callback,params) {
    log("SAVE");
    if($("textarea.note-area").val().length > 0) {
        $("#loading-screen").show();
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
        				log(data.insert_id);
        				if(data.insert_id !== null) {
            				ga('send', 'event', 'Notes', 'Save', 'New');
        	          $("body").attr("id",data.insert_id);
                    $(".view-note").attr("href","?note=" + data.insert_id);
        	          callback.apply(null,params);
        				} else {
            				ga('send', 'event', 'Notes', 'Save', 'Old');
            				$(".view-note").attr("href","?note=" + $("body").attr("id"));
        					callback.apply(null,params);
        				}
        			},
        			201: function() {
                $("#choose-user-type").fadeIn("fast");
        			},
        			500: function() {
            		ga('send', 'event', 'Notes', 'Error', 'New');
        				alert("Something went wrong saving your note - email tomasienrbc@gmail.com and yell at him about it");
        	    }
      		},
      		success:function(data) {
    	  		log(data.status);
      		},
            error:function() {
                log("error");
            }
        });
    }
}


// This function has too much mixed data / view logic in it. Call back or something required.
function publishNote() {
  $.ajax({
      url: "notes/publish",
      type: "POST",
      dataType: "json",
      data: {
          id: $("body").attr("id"),
          publish: $("body").attr("published")
      },
      success: function(data) {
          ga('send', 'event', 'Notes', 'Share', 'Publish');
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
          ga('send', 'event', 'Notes', 'Error', 'Publish');
          alert("SOMETHING WENT WRONG - email tomasienrbc@gmail.com and yell at him");
          log(data);
      }
  });
}

$(".guest-user").click(function() {
    createTempUser();
});

$(".permanent-user").click(function() {
    createNewUser();
});

$(".google-user").click(function() {
    createGoogleUser();
})

// toggle public / private
$(".single-note-publish").click(function(e) {
    e.preventDefault();
    publishNote();
});

function createTempUser() {
    ga('send', 'event', 'Users', 'New', 'Temporary');
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
  $(".signup-form").find("input[type=email], input[type=password]").val("");
	$(".signup-form").show();
  $(".login-form").hide();
}

function createGoogleUser() {

}

$("button.close-info").click(function(event) {
    event.preventDefault();
    $("#login-screen").fadeOut("fast");
});

// if signup form is submitted, block it and submit via AJAX
$("form.signup-form").submit(function(e) {
    ga('send', 'event', 'Users', 'New', 'Permanent');
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
	        }
        },
        error:function() {
            ga('send', 'event', 'Notes', 'Error', 'Signup');
	        log("error");
        }
    })
});

// password form
$("form.forgot-password-form").submit(function(event) {
    event.preventDefault();
    var form = $(this).serialize();
    $.ajax({
        url: "forgotPassword",
        type: "POST",
        dataType:"json",
        data: form,
        success:function(data) {
	        log(data);
	        log(data.success);
	        if(data.success) {
    	        // hide screens we don't need and set href of "view note"
    	      $("#login-screen").fadeOut("fast");
		        showSuccess("password reset email sent", 3000);
	        }
        },
        error:function(data) {
            ga('send', 'event', 'Notes', 'Error', 'Password Reset');
            log(data)
	        log("error");
        }
    });
    return false;
});

// click events for UI on add notes screen
$(".status-bar a.close").click(function() {
	hideSuccess("+ blank slate","slow");
});

// close the first time info screen
$(".info-close").click(function() {
    $("body").removeClass("menu-showing");
    $("textarea.note-area").focus();
});

// show info screen
$(".compose-info-button").click(function() {
	$("#info-screen").fadeIn("fast");
});

// hide info screen
$(".close-info").click(function() {
	$("#info-screen").fadeOut("fast");
});
$("#info-screen .overlay").click(function() {
	$("#info-screen").fadeOut("fast");
});

// show login screen
$(".login-button").click(function() {
	$("#login-screen").fadeIn("fast");
  $(".login-form").find("input[type=email], input[type=password]").val("");
	$(".login-form").show();
	$(".signup-form").hide();
	$(".login-form input[type=email]").focus();
});

// publish and copy share URL

$(".publish-note").mouseenter(function() {
  $(".top-left .message").hide();
  var url = getBaseURL();
  var shareID = $("body").attr("id");
  $(".top-left .share-url").text(url + "?note=" + shareID);
});

$(".publish-note").mouseleave(function() {
  $(".top-left .message").show();
  $(".top-left .share-url").text("");
});

$(".publish-note").click(function() {
  hideSuccess("+ blank slate","fast");
  if($("body").attr("published") == 0) {
    publishNote();
  }
  showSuccess("Note published and share URL copied","slow");
});

// JS Event for login form
$(".login-form").submit(function() {
    ga('send', 'event', 'Users', 'Returning', 'Login');
});

// hide login screen
$("#login-screen .overlay").click(function() {
	$("#login-screen").fadeOut("fast");
});

$(".forgot-password-link").click(function() {
    $(".login-form").hide();
    $(".forgot-password-form").show();
    $(".forgot-password-form input[type='email']").focus();
})

// show success
function showSuccess(text,speed) {
    $("#loading-screen").hide();
    log(text);
    log("show success");
    $("a.top-left").removeAttr('href');
	if($(".status-bar").hasClass("success")) {
    	$(".status-bar").fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);
	} else {
	    $(".status-bar").addClass("success");
        $(".success a.top-left .message").text(text);
	}
}

function hideSuccess(text) {
  $("a.top-left").attr('href',"/");
	$(".success a.top-left .message").text(text);
	$(".status-bar").removeClass("success");
	$("textarea.note-area").focus();
}

// GOOOOOOOGGGLLLE DOCS FUNCTIONALITY - SHOULD PROBABLY BE OWN FILE AND ONLY LOADED IF NEEDED

$(".save-google-doc-button").click(function() {
    saveGoogleDoc();
});

function saveGoogleDoc() {
    $("#loading-screen").show();
    log("save google doc wrapper");
    var subparams = ["note and gdoc saved!","slow"];
    var params = [showSuccess,subparams];
    saveNote(saveGoogleDocData,params);
}

function saveGoogleDocData(callback,params) {
    log("save google doc");
    if($("textarea.note-area").val().length > 0) {
        $.ajax({
            url: "google/addDoc",
            type:"POST",
            dataType:"JSON",
            data: {
                note_text:$("textarea.note-area").val(),
                id:$("body").attr("id")
            },
            statusCode: {
    			200: function(data) {
        			ga('send', 'event', 'Extensions', 'Google', 'New');
        			$(".view-external-link").addClass("show");
        			$(".view-external-link").text("google doc");
        			$(".view-external-link").attr("href",data.gdoc_link);
        			window.open(data.gdoc_link, '_blank');
        			callback.apply(null,params);
    				log(data);
    			},
    			201: function(data) {
        			ga('send', 'event', 'Extensions', 'Google', 'Auth');
                    log(data);
                    window.location = data.auth_url;
    			},
    			500: function() {
        			ga('send', 'event', 'Notes', 'Error', 'Google');
    				alert("Something went wrong saving your note - email tomasienrbc@gmail.com and yell at him about it");
    			}
      		},
      		success:function(data) {
    	  		log(data);
      		},
            error:function() {
                log("error");
            }
        })
    }
}

// PDF Functionality
$(".save-pdf-button").click(function() {
    savePDF();
});

function savePDF() {
    log("save google doc wrapper");
    var params = ["showSucces"];
    saveNote(savePDFData,params);
}

function savePDFData(callback) {
    ga('send', 'event', 'Extensions', 'PDF', 'New');
    document.location.href = "/pdf/create?id="+$("body").attr("id");
    showSuccess("Note saved and PDF downloading","slow");
}

if($(".view-external-link").hasClass("google-doc")) {
    $(".view-external-link").text("google doc");
    log("has google doc class, show success");
    showSuccess("Google Doc saved!","slow");
}

function toggleShowOutput() {
    $("body").toggleClass("show-output");
    $("textarea").focus();
    showHTMLOutput();
    resizeTextArea();
}

function resizeTextArea() {
    $("textarea").height("auto");
    log($('textarea').prop('scrollHeight'));
    $('textarea').height($('textarea').prop('scrollHeight'));
}
