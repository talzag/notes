var unorderedListArray = ["-"];
var showOutput = true;
// prevent tab functionality
$("textarea").keyup(function(){
    // real time output as HTML
    showHTMLOutput();
})

$("textarea").keydown(function(e) {
  
  //tabs
  var $this, end, start;
  if (e.keyCode === 9) {
    var content = this.value;
    var currentLineNumber = content.substr(0, this.selectionStart).split("\n").length;
    var currentCharNumber =  this.selectionStart;
    var contentLines = content.split("\n");
    var currentLineText = contentLines[currentLineNumber - 1];
    var firstCharLastLine = $.trim(currentLineText)[0];
    var beforeSpace = currentLineText.split(firstCharLastLine)[0];
    var contentString = "";      
    
    // TAB key works by adding a tab character 
    if(e.shiftKey) {
        tabBack(this,content,currentLineText,contentLines,currentLineNumber,beforeSpace,currentCharNumber);
        return false;
    }else {
        addTab(this,content,currentLineText,firstCharLastLine,contentLines,currentLineNumber,beforeSpace,currentCharNumber);
        return false;
    }        
    
  } else if(e.keyCode === 13) {

    // ENTER key for list continuation - this is a little messy and double code from above
      
        var content = this.value;
        var currentLineNumber = content.substr(0, this.selectionStart).split("\n").length;
        var currentCharNumber =  this.selectionStart;
        var contentLines = content.split("\n");
        var currentLineText = contentLines[currentLineNumber - 1];
        var firstCharLastLine = $.trim(currentLineText)[0];
        var secondCharLastLine = $.trim(currentLineText)[1];
        var firstTwoCharLastLine = firstCharLastLine + secondCharLastLine;
        var beforeSpace = currentLineText.split(firstCharLastLine)[0];
        var contentString = "";
        // if the first character of the previous line is in the "list array" we're probably in a list. Add a new list item
        // How do I make this so it also detects a "number." format, adds 1 to it, and then continues ordered lists too?
        if($.inArray(firstCharLastLine, unorderedListArray) > -1) {
            contentLines.splice(currentLineNumber,0,beforeSpace+firstCharLastLine+" ");
            for(var i=0;i<contentLines.length;i++) {
                contentString += contentLines[i];
                if(i !== contentLines.length - 1) {
                    contentString += "\n";
                }
            }
            $(this).val(contentString);
            // re-focus the cursor so we don't get lost!! 
            var tarea = document.getElementById('note-area');
            tarea.focus();
            tarea.selectionStart = currentCharNumber + beforeSpace.length + 3;
            tarea.selectionEnd = currentCharNumber + beforeSpace.length + 3; 
            return false;
        }
    }
});

function addTab(textArea,content,currentLineText,firstCharLastLine,contentLines,currentLineNumber,beforeSpace,currentCharNumber) {
    var numberOfLines = content.split(/\r|\r\n|\n/).length; 
    if($.inArray(firstCharLastLine, unorderedListArray) > -1) {
        var lineBreakChar = numberOfLines === 1 ? "" : "\n";
        var contentString = "";
        log(contentLines[currentLineNumber - 1]);
        contentLines[currentLineNumber - 1] = "\t"+currentLineText;
        log(contentLines);
        for(var i=0;i<contentLines.length;i++) {
            contentString += contentLines[i];
            if(i !== contentLines.length - 1) {
                contentString += "\n";
            }
        }
        $(textArea).val(contentString);
        // re-focus the cursor so we don't get lost!! 
        var tarea = document.getElementById('note-area');
        tarea.focus();
        tarea.selectionStart = currentCharNumber + 1;
        tarea.selectionEnd = currentCharNumber + 1; 
    }else {
        start = textArea.selectionStart;
        end = textArea.selectionEnd;
        $this = $(textArea);
        $this.val($this.val().substring(0, start) + "\t" + $this.val().substring(end));
        textArea.selectionStart = textArea.selectionEnd = start + 1;         
    } 
}

function tabBack(textArea,content,currentLineText,contentLines,currentLineNumber,beforeSpace,currentCharNumber) {
    log("tab back");
    var numberOfLines = content.split(/\r|\r\n|\n/).length; 
    var firstCharLastLine = currentLineText[0];
    if(firstCharLastLine === "\t") {
        log("first character correct");
        var lineBreakChar = numberOfLines === 1 ? "" : "\n";
        var contentString = "";
        log(contentLines[currentLineNumber - 1]);
        contentLines[currentLineNumber - 1] = currentLineText.substr(1);
        log(contentLines);
        for(var i=0;i<contentLines.length;i++) {
            contentString += contentLines[i];
            if(i !== contentLines.length - 1) {
                contentString += "\n";
            }
        }
        $(textArea).val(contentString);
        // re-focus the cursor so we don't get lost!! 
        var tarea = document.getElementById('note-area');
        tarea.focus();
        tarea.selectionStart = currentCharNumber - 1;
        tarea.selectionEnd = currentCharNumber - 1; 
    }
}

function showHTMLOutput() {
    $.ajax({
        url: "notes/markdown",
        type: "GET",
        dataType:"json",
        data: {
            markdown:$("textarea.note-area").val()
        },
        success:function(data) {
// 	       log(data);
	       $(".note-result").html(data.markdown);
        },
        error:function(data) {
//            log(data)
        }
    })
}