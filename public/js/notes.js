// slideout menu
$(".hamburger-icon").click(function() {
    if(!$(".menu-slide-out ul").is(":visible")) {
        $("body").addClass("menu-showing");
    } else {
        $("body").removeClass("menu-showing");
    }
});

// migrate temporary user to permanent user UI
$(".create-permanent-user a").click(function() {
    $(".menu-slide-out").removeClass("showing");
    $("#login-screen").fadeIn("fast");
    $("#login-screen form").find("input[type=email], input[type=password]").val("");
});

$("button.close-info").click(function(event) {
    event.preventDefault();
    $(".menu-slide-out").addClass("showing");
    $("#login-screen").fadeOut("fast");
});

// hide login screen
$("#login-screen .overlay").click(function() {
	$("#login-screen").fadeOut("fast");
});

// if signup form is submitted, block it and submit via AJAX
$("form.user-management-form").submit(function(e) {
    e.preventDefault();
    var form = $(this).serialize();
    $.ajax({
        url: "/users/create",
        type: "POST",
        dataType:"json",
        data: form,
        success:function(data) {
          console.log(data);
	        if(data.success) {
    	        // hide screens we don't need and set href of "view note"
                alert("User successfully migrated to a permanent user!");
                ga('send', 'event', 'Users', 'New', 'Permanent');
                $("#login-screen").fadeOut("fast");
	        }
        },
        error:function(xhr, status, error) {
             console.log(xhr.responseText);
        }
    });
});
