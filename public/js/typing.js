var unorderedListArray = ["-"];
// prevent tab functionality
$("textarea").keyup(function(){
    // real time output as HTML
    showHTMLOutput();
})

$("textarea").keydown(function(e) {
  // Still too much logic in the if statements.
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

    // ENTER key for list continuation - this is wrong and messy and double code from above

        var content = this.value;
        var currentLineNumber = content.substr(0, this.selectionStart).split("\n").length;
        var currentCharNumber =  this.selectionStart;
        var contentLines = content.split("\n");
        var currentLineText = contentLines[currentLineNumber - 1];
        // (Current Position) - (Length before this line) = position in current line
        var charactersBeforeCurrentLine = 0;
        for(var c = 0;c < currentLineNumber-1;c++) {
          charactersBeforeCurrentLine += contentLines[c].length;
        }
        charactersBeforeCurrentLine += (currentLineNumber-1);
        var currentLinePosition = currentCharNumber - charactersBeforeCurrentLine;
        // Split the current line into the text that should stay and the text for the next line
        var currentLineFirstPart = currentLineText.substr(0,currentLinePosition);
        var currentLineSecondPart = currentLineText.substr(currentLinePosition,currentLineText.length);

        var firstCharLastLine = $.trim(currentLineText)[0];
        var beforeSpace = currentLineText.split(firstCharLastLine)[0];
        var contentString = "";
        var orderedList = false;
         // if the first character of the previous line is in the "list array" we're probably in a list. Add a new list item
        if($.inArray(firstCharLastLine, unorderedListArray) > -1) {
            contentLines[currentLineNumber - 1] = currentLineFirstPart;
            contentLines.splice(currentLineNumber,0,beforeSpace+firstCharLastLine+" " + currentLineSecondPart);
            log(contentLines);
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
        } else if(orderedList) {
            // if ordered list, auto continue - this doesn't work yet
/*
            contentLines.splice(currentLineNumber,0,beforeSpace+firstCharLastLine+" ");
            log(contentLines);
            contentString = content + "\n" + "1. ";
*/
        }
    }

  if($("body").hasClass("show-output")) {
      resizeTextArea();
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
    if($("body").hasClass("show-output") !== false) {
      $.ajax({
          url: "notes/markdown",
          type: "POST",
          dataType:"json",
          data: {
              markdown:$("textarea.note-area").val()
          },
          success:function(data) {
  	       log(data);
  	       $(".note-result").html(data.markdown);
          },
          error:function(data) {
             log(data)
          }
      });
    }
}
