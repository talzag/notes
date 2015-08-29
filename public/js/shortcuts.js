$(document).keydown(function(e){
//     log(e.keyCode);
	// SAVE: Behavior for Control/Command S - SAVE	
    if ((e.metaKey == true || e.ctrlKey == true) && e.keyCode == 83) {
	    e.preventDefault();
        var params = ["note saved!","slow"];
        saveNote(showSuccess,params);
    } else if((e.metaKey == true || e.ctrlKey == true) && e.keyCode == 71) {
        e.preventDefault();
        saveGoogleDoc();
    } else if((e.metaKey == true || e.ctrlKey == true) && e.keyCode == 80) {
        e.preventDefault();
        savePDF();
    } else if((e.metaKey == true || e.ctrlKey == true) && e.shiftKey == true) {
        // footnotes
        log("this should be a footnote depending on the character");
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
    if($(".success").is(":visible") && e.keyCode !== 91) {
        hideSuccess("+ blank slate","slow");
    }
});